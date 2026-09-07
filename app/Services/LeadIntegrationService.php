<?php

namespace App\Services;

use App\Models\SystemIntegrationSetting;
use App\customer;
use App\inquiry;
use App\remarks;
use App\sales_reference;
use App\inquirytypes;
use App\User;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class LeadIntegrationService
{
    /**
     * Ingest a lead from Meta, API Webhook, or Website Form
     */
    public function ingestLead(array $data): array
    {
        $settings = SystemIntegrationSetting::getSettings();

        // 1. Sanitize & Normalize Inputs
        $name = trim($data['name'] ?? $data['customer_name'] ?? $data['full_name'] ?? 'Direct Online Lead');
        $phone = trim($data['phone'] ?? $data['customer_cell'] ?? $data['cell'] ?? $data['whatsapp'] ?? $data['phone_number'] ?? '');
        $email = trim($data['email'] ?? $data['customer_email'] ?? '');
        $city = trim($data['city'] ?? '');
        $country = trim($data['country'] ?? 'Pakistan');
        $serviceType = trim($data['service_type'] ?? $data['service'] ?? 'Umrah Package');
        $travelDate = !empty($data['travel_date']) ? date('Y-m-d', strtotime($data['travel_date'])) : null;
        $adults = intval($data['adults'] ?? $data['no_of_adults'] ?? 1);
        $children = intval($data['children'] ?? $data['no_of_children'] ?? 0);
        $infants = intval($data['infants'] ?? $data['no_of_infants'] ?? 0);
        $userNotes = trim($data['remarks'] ?? $data['message'] ?? $data['requirements'] ?? $data['notes'] ?? '');
        $sourceName = trim($data['source'] ?? $data['lead_source'] ?? 'Facebook Lead Ads');
        $campaignName = trim($data['campaign_name'] ?? $data['campaign'] ?? '');

        // 2. Round-Robin Sales Agent Assignment
        $assignedUser = $settings->getNextAssignedUser();
        $assignedUserId = $assignedUser ? $assignedUser->id : 1;
        $assignedUserName = $assignedUser ? $assignedUser->name : 'Administrator';

        // 3. Customer Resolution / Creation
        $customer = null;
        if (!empty($phone)) {
            $cleanedPhone = preg_replace('/[^0-9]/', '', $phone);
            $customer = customer::where('customer_cell', 'LIKE', '%' . substr($cleanedPhone, -9) . '%')->first();
        }
        if (!$customer && !empty($email)) {
            $customer = customer::where('customer_email', $email)->first();
        }

        if (!$customer) {
            $customer = customer::create([
                'customer_name' => $name,
                'customer_cell' => $phone ?: '0000000000',
                'customer_email' => $email ?: null,
                'whatsapp_number' => $phone ?: null,
                'city' => $city ?: null,
                'country' => $country ?: 'Pakistan',
                'customer_type' => 'Individual',
                'sale_person' => $assignedUserId,
                'created_by' => $assignedUserId,
                'status' => 1,
            ]);
        } else {
            // Update customer details if empty
            if (empty($customer->customer_email) && !empty($email)) {
                $customer->customer_email = $email;
            }
            if (empty($customer->city) && !empty($city)) {
                $customer->city = $city;
            }
            $customer->save();
        }

        // 4. Resolve Sales Reference (Lead Source ID)
        $salesRefId = $settings->default_sales_reference_id;
        $matchedRef = sales_reference::where('type_name', 'LIKE', '%' . $sourceName . '%')->first();
        if ($matchedRef) {
            $salesRefId = $matchedRef->type_id;
        } elseif (stripos($sourceName, 'facebook') !== false) {
            $fb = sales_reference::where('type_name', 'LIKE', '%Facebook%')->first();
            $salesRefId = $fb ? $fb->type_id : $salesRefId;
        } elseif (stripos($sourceName, 'instagram') !== false) {
            $ig = sales_reference::where('type_name', 'LIKE', '%Instagram%')->first();
            $salesRefId = $ig ? $ig->type_id : $salesRefId;
        } elseif (stripos($sourceName, 'google') !== false) {
            $gg = sales_reference::where('type_name', 'LIKE', '%Google%')->first();
            $salesRefId = $gg ? $gg->type_id : $salesRefId;
        } elseif (stripos($sourceName, 'whatsapp') !== false) {
            $wa = sales_reference::where('type_name', 'LIKE', '%WhatsApp%')->first();
            $salesRefId = $wa ? $wa->type_id : $salesRefId;
        }

        // 5. Resolve Inquiry Type ID
        $inquiryTypeId = $settings->default_inquiry_type_id;
        if (!empty($serviceType)) {
            $matchedType = inquirytypes::where('type_name', 'LIKE', '%' . $serviceType . '%')->first();
            if ($matchedType) {
                $inquiryTypeId = $matchedType->type_id;
            }
        }

        // 6. Build Structured Remarks
        $formattedRemarks = "⚡ DIRECT LEAD INGESTION (" . strtoupper($sourceName) . ")\n";
        if (!empty($campaignName)) {
            $formattedRemarks .= "Campaign: " . $campaignName . "\n";
        }
        $formattedRemarks .= "Requested Service: " . $serviceType . "\n";
        if ($travelDate) {
            $formattedRemarks .= "Expected Travel Date: " . date('d M Y', strtotime($travelDate)) . "\n";
        }
        $formattedRemarks .= "Passengers: {$adults} Adults, {$children} Children, {$infants} Infants\n";
        if (!empty($city)) {
            $formattedRemarks .= "Departure / City: " . $city . "\n";
        }
        if (!empty($userNotes)) {
            $formattedRemarks .= "Client Notes: " . $userNotes . "\n";
        }
        $formattedRemarks .= "Auto-Assigned Agent: " . $assignedUserName . " (Round-Robin Cycle)";

        // 7. Create Inquiry Record
        $inquiry = inquiry::create([
            'customer_id' => $customer->id_customers,
            'saleperson' => $assignedUserId,
            'sales_reference' => $salesRefId,
            'inquiry_type' => $inquiryTypeId,
            'remarks' => $formattedRemarks,
            'status' => $settings->default_inquiry_status ?: 'Open',
            'city' => $city ?: null,
            'travel_date' => $travelDate,
            'no_of_adults' => $adults,
            'no_of_children' => $children,
            'no_of_infants' => $infants,
            'created_by' => $assignedUserId,
        ]);

        // 8. Add Audit Remark in remarks table
        remarks::create([
            'inquiry_id' => $inquiry->id_inquiry,
            'remarks' => "Lead captured via " . $sourceName . " and automatically assigned to " . $assignedUserName . " via System Integration Round-Robin.",
            'remarks_status' => 'Open',
            'created_by' => $assignedUserId,
        ]);

        return [
            'success' => true,
            'inquiry_id' => $inquiry->id_inquiry,
            'customer_id' => $customer->id_customers,
            'customer_name' => $customer->customer_name,
            'assigned_agent_id' => $assignedUserId,
            'assigned_agent_name' => $assignedUserName,
            'source' => $sourceName,
            'created_at' => now()->toDateTimeString(),
        ];
    }

    /**
     * Process Meta (Facebook/Instagram) Leadgen Webhook Payload
     */
    public function processMetaWebhook(array $payload): array
    {
        $settings = SystemIntegrationSetting::getSettings();
        $results = [];

        if (empty($payload['entry'])) {
            return ['success' => false, 'message' => 'No entry in webhook payload'];
        }

        foreach ($payload['entry'] as $entry) {
            if (!empty($entry['changes'])) {
                foreach ($entry['changes'] as $change) {
                    if (($change['field'] ?? '') === 'leadgen' && !empty($change['value']['leadgen_id'])) {
                        $leadgenId = $change['value']['leadgen_id'];
                        $formId = $change['value']['form_id'] ?? '';
                        $pageId = $change['value']['page_id'] ?? '';

                        $leadData = $this->fetchMetaLeadData($leadgenId, $settings->meta_page_access_token);
                        $leadData['source'] = 'Facebook Lead Ads';
                        $leadData['campaign_name'] = 'Meta Form ID: ' . $formId;

                        $res = $this->ingestLead($leadData);
                        $results[] = $res;
                    }
                }
            }
        }

        return ['success' => true, 'processed' => count($results), 'results' => $results];
    }

    /**
     * Fetch full lead payload from Meta Graph API
     */
    protected function fetchMetaLeadData(string $leadgenId, ?string $accessToken): array
    {
        $data = [];
        if (empty($accessToken)) {
            return [
                'name' => 'Facebook Lead #' . $leadgenId,
                'phone' => '',
                'email' => '',
                'remarks' => 'Meta Leadgen ID: ' . $leadgenId . ' (Configure Page Access Token in System Integration Settings to auto-fetch answers)',
            ];
        }

        try {
            $response = Http::get("https://graph.facebook.com/v19.0/{$leadgenId}", [
                'access_token' => $accessToken,
            ]);

            if ($response->successful()) {
                $metaJson = $response->json();
                $fieldData = $metaJson['field_data'] ?? [];

                $customNotes = [];
                foreach ($fieldData as $field) {
                    $fieldName = strtolower($field['name'] ?? '');
                    $val = $field['values'][0] ?? '';

                    if (in_array($fieldName, ['full_name', 'name', 'first_name'])) {
                        $data['name'] = $val;
                    } elseif (in_array($fieldName, ['phone_number', 'phone', 'contact_number', 'whatsapp'])) {
                        $data['phone'] = $val;
                    } elseif (in_array($fieldName, ['email'])) {
                        $data['email'] = $val;
                    } elseif (in_array($fieldName, ['city'])) {
                        $data['city'] = $val;
                    } elseif (in_array($fieldName, ['service', 'service_type', 'package'])) {
                        $data['service_type'] = $val;
                    } else {
                        $customNotes[] = ucfirst(str_replace('_', ' ', $fieldName)) . ': ' . $val;
                    }
                }

                if (!empty($customNotes)) {
                    $data['remarks'] = implode("\n", $customNotes);
                }
            }
        } catch (\Throwable $e) {
            Log::error('Meta Graph API Fetch Exception: ' . $e->getMessage());
        }

        return $data;
    }
}
