<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WaterTariffController extends Controller
{
    public function index()
    {
        return view('water.index');
    }

    public function calculate(Request $request)
    {
        $request->validate([
            'consumption' => 'required|numeric|min:0',
            'type' => 'required|in:residencial,comercial,industrial',
        ]);

        $consumption = $request->input('consumption');
        $type = $request->input('type');
        
        $baseTariff = 0;
        $totalValue = 0;

        // Regras de Negócio por Tipo de Consumidor
        if ($type === 'residencial') {
            $baseTariff = 15.00;
            if ($consumption <= 10) {
                $totalValue = $baseTariff + ($consumption * 1.50);
            } elseif ($consumption <= 20) {
                $totalValue = $baseTariff + (10 * 1.50) + (($consumption - 10) * 2.50);
            } else {
                $totalValue = $baseTariff + (10 * 1.50) + (10 * 2.50) + (($consumption - 20) * 4.50);
            }
        } elseif ($type === 'comercial') {
            $baseTariff = 45.00;
            if ($consumption <= 20) {
                $totalValue = $baseTariff + ($consumption * 3.00);
            } else {
                $totalValue = $baseTariff + (20 * 3.00) + (($consumption - 20) * 5.50);
            }
        } elseif ($type === 'industrial') {
            $baseTariff = 90.00;
            if ($consumption <= 50) {
                $totalValue = $baseTariff + ($consumption * 5.00);
            } else {
                $totalValue = $baseTariff + (50 * 5.00) + (($consumption - 50) * 8.00);
            }
        }

        return view('water.index', compact('consumption', 'type', 'baseTariff', 'totalValue'));
    }
}
