<?php

namespace App\Services;

use App\Models\Integracao;
use App\Models\IntegracaoCredencial;
use Illuminate\Support\Str;

class IntegracaoService
{
    public function store(array $dados, ?array $credenciais = null): Integracao
    {
        $integracao = Integracao::create([
            'empresa_parametro_id' => $dados['empresa_parametro_id'],
            'nome' => $dados['nome'],
            'slug' => $this->gerarSlugUnico($dados['slug'] ?? $dados['nome']),
            'provider' => $dados['provider'] ?? null,
            'descricao' => $dados['descricao'] ?? null,
            'escopo' => $dados['escopo'],
            'tecnologia' => $dados['tecnologia'],
            'autenticacao' => $dados['autenticacao'],
            'autenticacao_especifica' => $dados['autenticacao_especifica'] ?? null,
            'endpoint' => $dados['endpoint'],
            'nativa' => $dados['nativa'] ?? false,
        ]);

        if ($credenciais !== null) {
            (new IntegracaoCredencialService())->sync($integracao->id, $credenciais);
        }

        return $integracao;
    }

    public function update(array $dados, int $id, ?array $credenciais = null): bool
    {
        $integracao = Integracao::findOrFail($id);

        $atualizado = $integracao->update([
            'empresa_parametro_id' => $dados['empresa_parametro_id'],
            'nome' => $dados['nome'],
            'slug' => $this->gerarSlugUnico($dados['slug'] ?? $dados['nome'], $id),
            'provider' => $dados['provider'] ?? null,
            'descricao' => $dados['descricao'] ?? null,
            'escopo' => $dados['escopo'],
            'tecnologia' => $dados['tecnologia'],
            'autenticacao' => $dados['autenticacao'],
            'autenticacao_especifica' => $dados['autenticacao_especifica'] ?? null,
            'endpoint' => $dados['endpoint'],
            'nativa' => $dados['nativa'] ?? false,
        ]);

        if ($dados['autenticacao'] === 'none') {
            IntegracaoCredencial::where('integracao_id', $integracao->id)->delete();
        } elseif ($credenciais !== null) {
            (new IntegracaoCredencialService())->sync($integracao->id, $credenciais);
        }

        return $atualizado;
    }

    public function destroy(int $id): bool
    {
        return Integracao::findOrFail($id)->delete();
    }

    public function restore(int $id): bool
    {
        return Integracao::withTrashed()->findOrFail($id)->restore();
    }

    private function gerarSlugUnico(string $valor, ?int $ignorarId = null): string
    {
        $slug = Str::slug($valor);
        $slugBase = $slug;
        $contador = 1;

        while (
            Integracao::withTrashed()
                ->when($ignorarId, fn ($q) => $q->where('id', '!=', $ignorarId))
                ->where('slug', $slug)
                ->exists()
        ) {
            $slug = $slugBase . '-' . $contador;
            $contador++;
        }

        return $slug;
    }
}
