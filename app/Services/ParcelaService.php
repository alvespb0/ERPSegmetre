<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Parcela;
use Carbon\Carbon;

class ParcelaService
{
    public function store(array $dados){
        return Parcela::create([
            'titulo_financeiro_id' => $dados['titulo_financeiro_id'] ?? null,
            'numero_parcela' => $dados['numero_parcela'],
            'valor' => $dados['valor'],
            'data_vencimento' => $dados['data_vencimento'],
            'status' => $dados['status']
        ]);
    }

    public function update(array $dados, $id){
        $parcela = Parcela::findOrFail($id);

        return $parcela->update([
            'titulo_financeiro_id' => $dados['titulo_financeiro_id'] ?? null,
            'numero_parcela' => $dados['numero_parcela'],
            'valor' => $dados['valor'],
            'data_vencimento' => $dados['data_vencimento'],
            'status' => $dados['status']
        ]);
    }

    public function show(){
        return Parcela::orderBy('data_vencimento', 'asc')->get();
    }

    public function destroy($id){
        $parcela = Parcela::findOrFail($id);

        return $parcela->delete();
    }

    public function showTrashed(){
        return Parcela::orderBy('data_vencimento', 'asc')
                        ->onlyTrashed()
                        ->get();
    }

    public function restore($id){
        return Parcela::withTrashed()->find($id)->restore();
    }

    public function gerarParcelas($valor_total, $quantidade_parcelas, $data_vencimento){
        $parcelas = [];
        $valorTotalCentavos = (int) round($valor_total * 100);
        $valorParcelaCentavos = intdiv($valorTotalCentavos, $quantidade_parcelas);

        $somaCentavos = $valorParcelaCentavos * $quantidade_parcelas;
        $diferencaCentavos = $valorTotalCentavos - $somaCentavos;

        for($i = 0; $i < $quantidade_parcelas; $i++){
            $valorCentavos = $valorParcelaCentavos;

            if ($i === $quantidade_parcelas - 1) {
                $valorCentavos += $diferencaCentavos;
            }

            $data = Carbon::parse($data_vencimento);
            $parcelas[] = [
                'parcela_numero' => $i + 1,
                'data_vencimento_parcela' => $data->addMonths($i)->format('Y-m-d'),
                'valor_parcela' => $valorCentavos/100,
            ];
        }

        return $parcelas;
    }
}