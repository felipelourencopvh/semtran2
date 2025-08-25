<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\{TipoAtividade, SituacaoAtividade, MedidaAtividade};

class TaxonomiaAtividadesSeeder extends Seeder
{
    public function run(): void
    {
        $json = <<<'JSON'
{
  "tipos": [
    {
      "id": "fiscalizacao_operacao_transito",
      "nome": "Fiscalização e Operação de Trânsito",
      "situacoes": [
        {
          "id": "fiscalizacao_rotina",
          "nome": "Fiscalização de Rotina",
          "medidas": [
            { "id": "abordagem_condutores", "nome": "Abordagem de condutores" },
            { "id": "patrulhamento_transito", "nome": "Patrulhamento de trânsito" },
            { "id": "fiscalizacao_estacionamento", "nome": "Fiscalização de estacionamento" },
            { "id": "ponto_base", "nome": "Fiscalização em ponto base" },
            { "id": "local_determinado", "nome": "Fiscalização em local determinado" },
            { "id": "apoio_interdicoes", "nome": "Apoio a interdições" },
            { "id": "controle_transito", "nome": "Controle de trânsito" },
            { "id": "interdicao_via", "nome": "Interdição de via" }
          ]
        },
        {
          "id": "operacoes_especiais",
          "nome": "Operações Especiais",
          "medidas": [
            { "id": "apoio_blitze", "nome": "Apoio em blitze" },
            { "id": "apoio_eventos", "nome": "Apoio a eventos" },
            { "id": "apoio_outros_orgaos", "nome": "Apoio com outros órgãos" },
            { "id": "apoio_grandes_eventos", "nome": "Apoio a grandes eventos" },
            { "id": "operacoes_feriados", "nome": "Operações em feriados e datas comemorativas" }
          ]
        },
        {
          "id": "ocorrencias",
          "nome": "Ocorrências",
          "medidas": [
            { "id": "sinalizacao_acidente", "nome": "Sinalização de locais de acidentes" },
            { "id": "apoio_acidente", "nome": "Apoio a acidente de trânsito" },
            { "id": "acionamento_emergencia", "nome": "Acionamento dos serviços de emergência" },
            { "id": "atendimento_ocorrencias", "nome": "Atendimento de ocorrências de trânsito" },
            { "id": "comunicacao_cco", "nome": "Comunicação via CCO" }
          ]
        }
      ]
    },
    {
      "id": "fiscalizacao_operacao_semaforica",
      "nome": "Fiscalização e Operação Semafórica",
      "situacoes": [
        {
          "id": "operacao_semaforica",
          "nome": "Operação semafórica",
          "medidas": [
            { "id": "apoio_manutencao_semaforo", "nome": "Apoio a manutenção de semáforo" },
            { "id": "apoio_empresa", "nome": "Apoio a empresa" },
            { "id": "sincronizacao", "nome": "Sincronização" },
            { "id": "controle_transito", "nome": "Controle de trânsito" },
            { "id": "interdicao_via", "nome": "Interdição de via" },
            { "id": "interdicao_cruzamento", "nome": "Interdição de cruzamento" },
            { "id": "ajuste_tempo", "nome": "Ajuste de tempo semafórico" }
          ]
        },
        {
          "id": "monitoramento_controle",
          "nome": "Monitoramento e Controle",
          "medidas": [
            { "id": "monitoramento_rotina", "nome": "Monitoramento de rotina" },
            { "id": "reset_online", "nome": "Reset semafórico online" },
            { "id": "reset_local", "nome": "Reset semafórico local" },
            { "id": "controle_manual", "nome": "Controle manual de semáforo" },
            { "id": "modo_operador", "nome": "Ativação modo operador" },
            { "id": "modo_piscante", "nome": "Ativação de modo piscante" }
          ]
        }
      ]
    },
    {
      "id": "dados_estatisticos",
      "nome": "Dados Estatísticos",
      "situacoes": [
        {
          "id": "coleta_analise",
          "nome": "Coleta e Análise",
          "medidas": [
            { "id": "contagem_volumetrica", "nome": "Contagem volumétrica" },
            { "id": "contagens_geral", "nome": "Contagens em geral" },
            { "id": "contagens_pedestres", "nome": "Contagens de pedestres" },
            { "id": "contagens_fila_espera", "nome": "Contagens de fila de espera" },
            { "id": "contagens_ciclo_vazio", "nome": "Contagens de ciclo vazio" },
            { "id": "analise_acidentes", "nome": "Análise de causas e locais de acidentes" },
            { "id": "relatorios_estatisticos", "nome": "Elaboração de relatórios estatísticos" },
            { "id": "mapeamento_pontos_criticos", "nome": "Mapeamento de pontos críticos de acidentes" },
            { "id": "banco_dados_geo", "nome": "Criação de banco de dados georreferenciado" }
          ]
        }
      ]
    },
    {
      "id": "educacao_transito",
      "nome": "Educação de Trânsito",
      "situacoes": [
        {
          "id": "campanhas_conscientizacao",
          "nome": "Campanhas de Conscientização",
          "medidas": [
            { "id": "campanhas_midias", "nome": "Campanhas em mídias diversas (TV, rádio, web)" },
            { "id": "distribuicao_material", "nome": "Distribuição de material educativo" },
            { "id": "acoes_grande_publico", "nome": "Ações em locais de grande público" },
            { "id": "simulacoes_acidentes", "nome": "Simulações de acidentes e seus impactos" },
            { "id": "parcerias_orgaos", "nome": "Parcerias com outros órgãos" },
            { "id": "panfletagem", "nome": "Panfletagem educativa" }
          ]
        },
        {
          "id": "programas_educacionais",
          "nome": "Programas Educacionais",
          "medidas": [
            { "id": "programas_escolas", "nome": "Programas em escolas e universidades" },
            { "id": "cursos_infratores", "nome": "Cursos para condutores infratores" },
            { "id": "palestras_publicos", "nome": "Palestras para pedestres, ciclistas e motociclistas" },
            { "id": "capacitacao_professores", "nome": "Capacitação de professores e agentes de trânsito" },
            { "id": "minicidades", "nome": "Criação de minicidades de trânsito para crianças" }
          ]
        },
        {
          "id": "acoes_empresas_comunidades",
          "nome": "Ações em Empresas e Comunidades",
          "medidas": [
            { "id": "palestras_empresas", "nome": "Palestras em empresas" },
            { "id": "acoes_bairros", "nome": "Ações em associações de bairro" },
            { "id": "incentivo_projetos", "nome": "Incentivo a projetos de segurança no trânsito" }
          ]
        }
      ]
    }
  ]
}
JSON;

        $root = json_decode($json, true);

        if (json_last_error() !== JSON_ERROR_NONE || !is_array($root)) {
            throw new \RuntimeException('JSON inválido no TaxonomiaAtividadesSeeder: ' . json_last_error_msg());
        }
        if (!isset($root['tipos']) || !is_array($root['tipos'])) {
            throw new \RuntimeException('Estrutura esperada não encontrada: chave "tipos" ausente ou inválida.');
        }

        foreach ($root['tipos'] as $tipo) {
            $t = TipoAtividade::updateOrCreate(
                ['slug' => $tipo['id']],
                ['nome' => $tipo['nome']]
            );

            foreach ($tipo['situacoes'] as $sit) {
                $s = SituacaoAtividade::updateOrCreate(
                    ['slug' => $sit['id']],
                    ['nome' => $sit['nome'], 'tipo_atividade_id' => $t->id]
                );

                foreach ($sit['medidas'] as $med) {
                    MedidaAtividade::updateOrCreate(
                        ['slug' => $med['id']],
                        ['nome' => $med['nome'], 'situacao_atividade_id' => $s->id]
                    );
                }
            }
        }
    }
}
