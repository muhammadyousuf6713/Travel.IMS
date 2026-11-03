<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class CurrencyController extends Controller
{
    /**
     * Display a listing of currencies and exchange rates.
     */
    public function index()
    {
        Currency::ensureTableExists();
        $currencies = Currency::orderBy('is_default', 'desc')->orderBy('code', 'asc')->get();

        return view('currencies.index', compact('currencies'));
    }

    /**
     * Store a newly created currency in storage.
     */
    public function store(Request $request)
    {
        Currency::ensureTableExists();

        $request->validate([
            'code' => 'required|string|max:10|unique:currencies,code',
            'name' => 'required|string|max:100',
            'symbol' => 'nullable|string|max:20',
            'exchange_rate' => 'required|numeric|min:0.0001',
        ]);

        $code = strtoupper(trim($request->code));
        $isDefault = $request->boolean('is_default');

        if ($isDefault) {
            Currency::where('is_default', 1)->update(['is_default' => 0]);
        }

        Currency::create([
            'code' => $code,
            'name' => trim($request->name),
            'symbol' => trim($request->symbol ?? $code),
            'exchange_rate' => $request->exchange_rate,
            'is_default' => $isDefault,
            'status' => $request->boolean('status', true),
        ]);

        Session::flash('success', "Currency {$code} added successfully.");
        return redirect()->route('currencies.index');
    }

    /**
     * Update the specified currency in storage.
     */
    public function update(Request $request, $id)
    {
        Currency::ensureTableExists();
        $currency = Currency::findOrFail($id);

        $request->validate([
            'code' => 'required|string|max:10|unique:currencies,code,' . $currency->id,
            'name' => 'required|string|max:100',
            'symbol' => 'nullable|string|max:20',
            'exchange_rate' => 'required|numeric|min:0.0001',
        ]);

        $code = strtoupper(trim($request->code));
        $isDefault = $request->boolean('is_default');

        if ($isDefault) {
            Currency::where('id', '!=', $currency->id)->where('is_default', 1)->update(['is_default' => 0]);
        }

        $currency->update([
            'code' => $code,
            'name' => trim($request->name),
            'symbol' => trim($request->symbol ?? $code),
            'exchange_rate' => $request->exchange_rate,
            'is_default' => $isDefault,
            'status' => $request->boolean('status', true),
        ]);

        Session::flash('success', "Currency {$code} updated successfully.");
        return redirect()->route('currencies.index');
    }

    /**
     * Remove the specified currency from storage.
     */
    public function destroy($id)
    {
        Currency::ensureTableExists();
        $currency = Currency::findOrFail($id);

        if ($currency->is_default) {
            Session::flash('error', "Cannot delete the default base currency ({$currency->code}). Please designate another default first.");
            return redirect()->route('currencies.index');
        }

        $code = $currency->code;
        $currency->delete();

        Session::flash('success', "Currency {$code} removed successfully.");
        return redirect()->route('currencies.index');
    }

    /**
     * Return JSON dictionary of active currencies and rates.
     */
    public function ratesJson()
    {
        $currencies = Currency::getActiveCurrencies();
        return response()->json($currencies);
    }
}
