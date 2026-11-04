<?php

namespace App\Http\Controllers;

use App\Models\OrganizationSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class OrganizationSettingController extends Controller
{
    public function edit()
    {
        $setting = OrganizationSetting::first();
        if (!$setting) {
            $setting = OrganizationSetting::create([
                'name' => 'Flugzeit Aviation',
                'theme_color' => '#cb0c9f'
            ]);
        }
        $cities = $setting->city ? \App\cities::where('name', $setting->city)->get() : collect([]);
        return view('organization_settings.edit', compact('setting', 'cities'));
    }

    public function update(Request $request)
    {
        $setting = OrganizationSetting::first();
        if (!$setting) {
            $setting = new OrganizationSetting();
        }

        $setting->name = $request->input('name');
        $setting->theme_color = $request->input('theme_color');
        $setting->website = $request->input('website');
        $setting->email = $request->input('email');
        $setting->phone = $request->input('phone');
        $setting->address = $request->input('address');
        $setting->city = $request->input('city');
        $setting->logo_height = $request->filled('logo_height') ? (int)$request->input('logo_height') : 55;
        $setting->logo_width = $request->filled('logo_width') ? (int)$request->input('logo_width') : 180;
        $setting->logo_padding = $request->filled('logo_padding') ? (int)$request->input('logo_padding') : 4;
        $setting->logo_margin_top = $request->filled('logo_margin_top') ? (int)$request->input('logo_margin_top') : 0;
        $setting->logo_margin_bottom = $request->filled('logo_margin_bottom') ? (int)$request->input('logo_margin_bottom') : 0;

        if ($request->hasFile('logo')) {
            $logo = $request->file('logo');
            $logoName = time() . '_logo.' . $logo->getClientOriginalExtension();
            $logo->move(public_path('uploads/settings'), $logoName);
            $setting->logo = 'uploads/settings/' . $logoName;
        }

        if ($request->hasFile('favicon')) {
            $favicon = $request->file('favicon');
            $faviconName = time() . '_favicon.' . $favicon->getClientOriginalExtension();
            $favicon->move(public_path('uploads/settings'), $faviconName);
            $setting->favicon = 'uploads/settings/' . $faviconName;
        }

        if ($request->hasFile('login_bg')) {
            $bg = $request->file('login_bg');
            $bgName = time() . '_bg.' . $bg->getClientOriginalExtension();
            $bg->move(public_path('uploads/settings'), $bgName);
            $setting->login_bg = 'uploads/settings/' . $bgName;
        }

        $setting->save();

        return redirect()->back()->with('success', 'Organization settings updated successfully!');
    }
}
