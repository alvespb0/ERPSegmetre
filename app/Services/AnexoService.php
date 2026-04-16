<?php
namespace App\Services;

use Illuminate\Support\Facades\Log;

use App\Models\Anexo;
use App\Models\Movimentacao;
use App\Models\Parcela;
use App\Models\TituloFinanceiro;

use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class AnexoService
{
    public function criarAnexoMovimentacao(Movimentacao $movimentacao, $file, $tipo, $descricao = null){
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs(
            "anexos/movimentacao/{$movimentacao->id}",
            $fileName, 'public'
        );

        try{
            return $movimentacao->anexos()->create([
                'descricao' => $descricao ?? null,
                'path' => $path,
                'tipo' => $tipo,
            ]);
        }catch(\Throwable $e){
            \Log::error('Erro ao fazer upload de anexo', [
                'exception' => $e,
                'movimentacao_id' => $movimentacao->id,
                'path' => $path ?? null,
            ]);
            Storage::disk('public')->delete($path);
            throw $e;
        }
    }

    public function criarAnexoParcela(Parcela $parcela, $file, $tipo, $descricao = null){
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs(
            "anexos/parcela/{$parcela->id}",
            $fileName,
            'public'
        );

        try {
            return $parcela->anexos()->create([
                'descricao' => $descricao,
                'path' => $path,
                'tipo' => $tipo
            ]);
        } catch (\Throwable $e) {
            \Log::error('Erro ao fazer upload de anexo', [
                'exception' => $e,
                'parcela_id' => $parcela->id,
                'path' => $path ?? null,
            ]);
            Storage::disk('public')->delete($path);
            throw $e;
        }
    }

    public function criarAnexoTitulo(TituloFinanceiro $titulo, $file, $tipo, $descricao = null){
        $fileName = Str::uuid() . '.' . $file->getClientOriginalExtension();

        $path = $file->storeAs(
            "anexos/titulo/{$titulo->id}",
            $fileName,
            'public'
        );

        try {
            return $titulo->anexos()->create([
                'descricao' => $descricao,
                'path' => $path,
                'tipo' => $tipo
            ]);
        } catch (\Throwable $e) {
            \Log::error('Erro ao fazer upload de anexo', [
                'exception' => $e,
                'titulo_id' => $titulo->id,
                'path' => $path ?? null,
            ]);
            Storage::disk('public')->delete($path);
            throw $e;
        }
    }

    public function download($anexoId){
        $anexo = Anexo::findOrFail($anexoId);

        return Storage::disk('public')->download($anexo->path);
    }

    public function destroy($anexoId){
        return Anexo::findOrFail($anexoId)->delete();
    }
}