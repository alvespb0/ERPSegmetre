<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;
use App\Models\Anexo;

use App\Models\Movimentacao;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class AnexoService
{
    public function criarAnexoMovimentacao(Movimentacao $movimentacao, $file, $tipo, $descricao = null){
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs(
            "anexos/movimentacao/{$movimentacao->id}",
            $fileName, 'public'
        );

        return $movimentacao->anexos()->create([
            'descricao' => $descricao ?? null,
            'path' => $path,
            'tipo' => $tipo,
        ]);
    }

    public function download($anexoId){
        $anexo = Anexo::findOrFail($anexoId);

        return Storage::disk('public')->download($anexo->path);
    }

    public function destroy($anexoId){
        return Anexo::findOrFail($anexoId)->delete();
    }
}