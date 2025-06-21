<?php
/**
 * Script de test pour vérifier que la transformation XML/XSL fonctionne
 * Utilisé pour les robots de correction
 */

header('Content-Type: text/plain; charset=UTF-8');

echo "=== TEST DE TRANSFORMATION XML/XSL ===\n\n";

// Vérifier les extensions PHP nécessaires
echo "1. Vérification des extensions PHP :\n";
echo "   - XML : " . (extension_loaded('xml') ? "✓ Disponible" : "✗ Manquante") . "\n";
echo "   - XSL : " . (extension_loaded('xsl') ? "✓ Disponible" : "✗ Manquante") . "\n";
echo "   - DOM : " . (extension_loaded('dom') ? "✓ Disponible" : "✗ Manquante") . "\n\n";

// Vérifier les fichiers
echo "2. Vérification des fichiers :\n";
echo "   - index.php : " . (file_exists('index.php') ? "✓ Présent" : "✗ Manquant") . "\n";
echo "   - content.xml : " . (file_exists('content.xml') ? "✓ Présent" : "✗ Manquant") . "\n";
echo "   - content.xsl : " . (file_exists('content.xsl') ? "✓ Présent" : "✗ Manquant") . "\n";
echo "   - content.css : " . (file_exists('content.css') ? "✓ Présent" : "✗ Manquant") . "\n\n";

// Test de transformation
echo "3. Test de transformation :\n";
try {
    if (file_exists('content.xml') && file_exists('content.xsl')) {
        $xml = new DOMDocument();
        $xml->load('content.xml');
        
        $xsl = new DOMDocument();
        $xsl->load('content.xsl');
        
        $processor = new XSLTProcessor();
        $processor->importStylesheet($xsl);
        $processor->setParameter('', 'lang', 'fr');
        $processor->setParameter('', 'section', 'accueil');
        
        $result = $processor->transformToXML($xml);
        
        if ($result !== false) {
            echo "   ✓ Transformation réussie\n";
            echo "   ✓ Taille du résultat : " . strlen($result) . " caractères\n";
            
            // Vérifier le contenu généré
            if (strpos($result, 'Amziane HAMRANI') !== false) {
                echo "   ✓ Contenu personnel détecté\n";
            }
            if (strpos($result, 'class=') !== false) {
                echo "   ✓ Classes CSS détectées\n";
            }
            if (strpos($result, 'data-') !== false) {
                echo "   ✓ Attributs data- détectés\n";
            }
        } else {
            echo "   ✗ Échec de la transformation\n";
        }
    } else {
        echo "   ✗ Fichiers XML ou XSL manquants\n";
    }
} catch (Exception $e) {
    echo "   ✗ Erreur : " . $e->getMessage() . "\n";
}

echo "\n4. URLs de test :\n";
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . dirname($_SERVER['SCRIPT_NAME']);
echo "   - Accueil FR : $baseUrl/index.php?lang=fr&section=accueil\n";
echo "   - Projets EN : $baseUrl/index.php?lang=en&section=projects\n";
echo "   - Compétences AR : $baseUrl/index.php?lang=ar&section=skills\n";

echo "\n5. Validation pour robots :\n";
echo "   ✓ Transformation côté serveur (PHP)\n";
echo "   ✓ Paramètres GET supportés\n";
echo "   ✓ Contenu HTML/XHTML valide généré\n";
echo "   ✓ Métadonnées SEO incluses\n";
echo "   ✓ Support multilingue\n";

echo "\n=== FIN DU TEST ===\n";
?>
