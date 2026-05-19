<?php
require_once __DIR__ . '/../bootstrap.php';
$pageTitle = $lang === 'es' ? 'CyberEscudo — MITRE ATT&CK Visual Mapper' : 'CyberEscudo — MITRE ATT&CK Visual Mapper';
$pageDescription = $lang === 'es' 
    ? 'Mapeador interactivo del framework MITRE ATT&CK. Busca tácticas, grupos APT y descubre reglas de detección KQL.' 
    : 'Interactive MITRE ATT&CK framework mapper. Search tactics, APT groups, and discover KQL detection rules.';
require __DIR__ . '/../templates/header.php';
?>

<style>
    /* ─── ESTILOS MITRE ATT&CK MAPPER ─── */
    .mitre-container { max-width: 1400px; margin: 3rem auto; padding: 0 1.5rem; }
    
    .mitre-header { text-align: center; margin-bottom: 3rem; }
    .mitre-title { font-family: var(--mono); color: var(--cyan); font-size: clamp(2rem, 5vw, 3rem); text-transform: uppercase; letter-spacing: 2px; text-shadow: 0 0 20px rgba(0,255,255,0.3); margin-bottom: 1rem; }
    
    /* Buscador */
    .mitre-search-box { 
        background: rgba(0, 0, 0, 0.6); border: 1px solid var(--cyan); border-radius: 8px; 
        padding: 1.5rem; display: flex; gap: 1rem; margin-bottom: 2rem; box-shadow: 0 0 20px rgba(0,255,255,0.1);
    }
    .mitre-search-input {
        flex: 1; background: rgba(255,255,255,0.05); border: 1px solid var(--border); border-radius: 4px;
        padding: 1rem 1.5rem; color: #fff; font-family: var(--mono); font-size: 1rem; outline: none; transition: all 0.3s;
    }
    .mitre-search-input:focus { border-color: var(--cyan); box-shadow: inset 0 0 10px rgba(0,255,255,0.2); }
    
    /* Layout Principal: Matriz + Detalles */
    .mitre-layout { display: grid; grid-template-columns: 1fr; gap: 2rem; align-items: start; }
    @media (min-width: 1024px) { .mitre-layout { grid-template-columns: 3fr 1fr; } }
    
    /* La Matriz (Grid Horizontal) */
    .matrix-board { 
        display: flex; gap: 1rem; overflow-x: auto; padding-bottom: 1rem; 
        scrollbar-width: thin; scrollbar-color: var(--cyan) rgba(0,0,0,0.3);
    }
    .matrix-board::-webkit-scrollbar { height: 8px; }
    .matrix-board::-webkit-scrollbar-thumb { background: var(--cyan); border-radius: 4px; }
    
    .tactic-col { 
        min-width: 220px; background: rgba(10, 15, 20, 0.8); border: 1px solid var(--border); 
        border-radius: 6px; display: flex; flex-direction: column; overflow: hidden;
    }
    .tactic-header { 
        background: rgba(0, 255, 255, 0.1); border-bottom: 1px solid var(--cyan); 
        padding: 0.8rem; text-align: center; font-family: var(--mono); font-weight: bold; color: var(--white); font-size: 0.85rem; text-transform: uppercase;
    }
    .tactic-techniques { padding: 0.8rem; display: flex; flex-direction: column; gap: 0.5rem; }
    
    /* Tarjetas de Técnicas */
    .tech-card { 
        background: rgba(0,0,0,0.5); border: 1px solid var(--border); border-left: 3px solid transparent;
        padding: 0.6rem; border-radius: 4px; cursor: pointer; transition: all 0.2s; font-family: var(--font);
    }
    .tech-card:hover { border-color: rgba(0,255,255,0.5); border-left-color: var(--cyan); transform: translateX(3px); background: rgba(0,255,255,0.05); }
    .tech-id { font-family: var(--mono); font-size: 0.7rem; color: var(--cyan); margin-bottom: 0.2rem; }
    .tech-name { font-size: 0.85rem; color: #d4d4d4; font-weight: 500; }
    
    /* Clases dinámicas de filtrado */
    .tech-card.dimmed { opacity: 0.15; filter: grayscale(100%); pointer-events: none; }
    .tech-card.highlight { border-left-color: #ff2a2a; background: rgba(255, 42, 42, 0.1); border-color: rgba(255, 42, 42, 0.3); box-shadow: 0 0 10px rgba(255, 42, 42, 0.2); }
    .tech-card.highlight .tech-id { color: #ff2a2a; }
    .tech-card.highlight .tech-name { color: #fff; }

    /* Panel de Detalles Laterales */
    .detail-panel { 
        background: rgba(5, 10, 15, 0.95); border: 1px solid var(--cyan); border-radius: 8px; 
        padding: 1.5rem; box-shadow: 0 0 30px rgba(0,255,255,0.05); position: sticky; top: 100px; min-height: 400px;
    }
    .detail-placeholder { text-align: center; color: var(--gray); font-family: var(--mono); margin-top: 50%; transform: translateY(-50%); }
    
    .dt-id { font-family: var(--mono); color: var(--cyan); font-size: 1.2rem; font-weight: bold; margin-bottom: 0.5rem;}
    .dt-name { font-size: 1.5rem; color: #fff; margin-bottom: 1.5rem; font-weight: 800;}
    .dt-section { margin-bottom: 1.5rem; }
    .dt-label { font-family: var(--mono); font-size: 0.7rem; color: var(--gray); text-transform: uppercase; letter-spacing: 1px; border-bottom: 1px solid rgba(255,255,255,0.1); padding-bottom: 0.3rem; margin-bottom: 0.8rem; }
    .dt-text { font-size: 0.9rem; color: #ccc; line-height: 1.5; }
    
    .apt-badge { display: inline-block; background: rgba(255, 42, 42, 0.15); border: 1px solid rgba(255, 42, 42, 0.4); color: #ff2a2a; font-family: var(--mono); font-size: 0.75rem; font-weight: bold; padding: 0.3rem 0.6rem; border-radius: 4px; margin: 0 0.5rem 0.5rem 0; }
    .kql-btn { display: inline-flex; align-items: center; gap: 0.5rem; background: rgba(0, 212, 90, 0.1); border: 1px solid #00d45a; color: #00d45a; font-family: var(--mono); font-size: 0.8rem; font-weight: bold; padding: 0.6rem 1rem; border-radius: 4px; text-decoration: none; transition: all 0.2s; margin-top: 0.5rem;}
    .kql-btn:hover { background: #00d45a; color: #000; box-shadow: 0 0 15px rgba(0, 212, 90, 0.4); }
</style>

<div class="mitre-container" id="mitre-mapper" data-lang="<?= $lang ?>" data-url="<?= BASE_URL ?>">
    
    <div style="display: flex; justify-content: flex-end; margin-bottom: 1rem;">
        <div style="background: rgba(0,255,0,0.1); border: 1px solid #00ff00; color: #00ff00; padding: 0.4rem 1rem; border-radius: 999px; font-family: var(--mono); font-size: 0.75rem; display: flex; align-items: center; gap: 0.5rem; box-shadow: 0 0 10px rgba(0,255,0,0.2);">
            <div style="width: 8px; height: 8px; background: #00ff00; border-radius: 50%; animation: pulse 1.5s infinite;"></div>
            <span id="sync-status"><?= $lang === 'es' ? 'Verificando sincronización...' : 'Checking sync status...' ?></span>
        </div>
    </div>

    <div class="mitre-header">
        <h1 class="mitre-title">MITRE ATT&CK Visual Mapper</h1>
        <p style="color: var(--gray); max-width: 700px; margin: 0 auto; font-size: 1.1rem;">
            <?= $lang === 'es' ? 'Motor de inteligencia conectado directamente con MITRE Corporation. Selecciona técnicas para analizar su contención.' : 'Intelligence engine connected directly with MITRE Corporation. Select techniques to analyze their containment.' ?>
        </p>
    </div>

    <div class="mitre-search-box">
        <input type="text" id="mitre-search" class="mitre-search-input" placeholder="<?= $lang === 'es' ? '🔍 Buscar técnica (ej. Pass the Hash, T1059, Windows...)' : '🔍 Search technique (e.g. Pass the Hash, T1059, Windows...)' ?>">
    </div>

    <div class="mitre-layout">
        <div class="matrix-board" id="matrix-board">
            </div>

        <div class="detail-panel" id="detail-panel">
            <div class="detail-placeholder">
                <div style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.5;">📡</div>
                <?= $lang === 'es' ? 'Selecciona o busca una técnica en la matriz para analizar sus datos desde la API.' : 'Select or search a technique in the matrix to analyze its API data.' ?>
            </div>
        </div>
    </div>
</div>

<?php require __DIR__ . '/../templates/footer.php'; ?>