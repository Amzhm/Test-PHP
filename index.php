<?php
// Configuration et validation des paramètres
$validLangs = ['fr', 'en', 'ar'];
$validSections = ['accueil', 'infos', 'educations', 'experiences', 'projects', 'skills', 'interests'];

$lang = isset($_GET['lang']) && in_array($_GET['lang'], $validLangs) ? $_GET['lang'] : 'fr';
$section = isset($_GET['section']) && in_array($_GET['section'], $validSections) ? $_GET['section'] : 'accueil';

// Fonction de transformation XML/XSL
function transformXMLWithXSL($xmlFile, $xslFile, $lang, $section) {
    // Charger le document XML
    $xml = new DOMDocument();
    if (!$xml->load($xmlFile)) {
        throw new Exception("Erreur lors du chargement du fichier XML: $xmlFile");
    }
    
    // Charger la feuille de style XSL
    $xsl = new DOMDocument();
    if (!$xsl->load($xslFile)) {
        throw new Exception("Erreur lors du chargement du fichier XSL: $xslFile");
    }
    
    // Créer le processeur XSLT
    $processor = new XSLTProcessor();
    
    // Importer la feuille de style
    if (!$processor->importStylesheet($xsl)) {
        throw new Exception("Erreur lors de l'importation de la feuille de style XSL");
    }
    
    // Définir les paramètres
    $processor->setParameter('', 'lang', $lang);
    $processor->setParameter('', 'section', $section);
    
    // Effectuer la transformation
    $result = $processor->transformToXML($xml);
    
    if ($result === false) {
        throw new Exception("Erreur lors de la transformation XML/XSL");
    }
    
    return $result;
}

// Définir les en-têtes HTTP appropriés
header('Content-Type: text/html; charset=UTF-8');
header('Cache-Control: no-cache, must-revalidate');

// Générer les URLs pour les liens hreflang
$baseUrl = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'] . $_SERVER['SCRIPT_NAME'];

try {
    // Effectuer la transformation XML/XSL
    $transformedContent = transformXMLWithXSL('content.xml', 'content.xsl', $lang, $section);
    
    // Générer le HTML complet
    ?>
<!DOCTYPE html>
<html lang="<?php echo htmlspecialchars($lang); ?>" xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Portfolio XML/XSL - Amziane HAMRANI</title>
    <meta name="description" content="Portfolio multilingue d'Amziane HAMRANI, étudiant ingénieur. Démonstration de compétences en XML, XSL et technologies du Web Sémantique." />
    <meta name="author" content="Amziane HAMRANI" />
    <meta name="keywords" content="Portfolio, XML, XSL, XSLT, Web Sémantique, Ingénieur, Informatique" />
    
    <!-- Liens hreflang pour le SEO multilingue -->
    <link rel="alternate" href="<?php echo $baseUrl; ?>?lang=fr&amp;section=<?php echo $section; ?>" hreflang="fr" />
    <link rel="alternate" href="<?php echo $baseUrl; ?>?lang=en&amp;section=<?php echo $section; ?>" hreflang="en" />
    <link rel="alternate" href="<?php echo $baseUrl; ?>?lang=ar&amp;section=<?php echo $section; ?>" hreflang="ar" />
    <link rel="alternate" href="<?php echo $baseUrl; ?>?lang=fr&amp;section=<?php echo $section; ?>" hreflang="x-default" />
    
    <!-- Liens canoniques -->
    <link rel="canonical" href="<?php echo $baseUrl; ?>?lang=<?php echo $lang; ?>&amp;section=<?php echo $section; ?>" />
    
    <!-- CSS -->
    <link rel="stylesheet" href="content.css" />
    
    <!-- Métadonnées techniques -->
    <meta name="generator" content="PHP XSL Transformation" />
    <meta name="robots" content="index, follow" />
</head>
<body>
    <!-- Contenu généré par transformation XML/XSL côté serveur -->
    <div id="app">
        <?php echo $transformedContent; ?>
    </div>
    
    <!-- JavaScript pour améliorer l'expérience utilisateur -->
    <script>
        // Configuration
        const currentLang = '<?php echo $lang; ?>';
        const currentSection = '<?php echo $section; ?>';
        
        // Amélioration progressive : si JavaScript est activé, on peut ajouter des fonctionnalités
        document.addEventListener('DOMContentLoaded', function() {
            // Marquer le bouton de langue actuel
            const currentLangBtn = document.querySelector(`.lang-btn[data-lang="${currentLang}"]`);
            if (currentLangBtn) {
                currentLangBtn.classList.add('selected');
            }
            
            // Gérer les clics sur les boutons de langue
            document.querySelectorAll('.lang-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const newLang = this.getAttribute('data-lang');
                    if (newLang !== currentLang) {
                        // Redirection côté serveur pour transformation
                        window.location.href = `?lang=${newLang}&section=${currentSection}`;
                    }
                });
            });
            
            // Gérer les clics sur les liens de menu
            document.querySelectorAll('.menu-link').forEach(link => {
                link.addEventListener('click', function(e) {
                    e.preventDefault();
                    const newSection = this.getAttribute('data-section');
                    if (newSection !== currentSection) {
                        // Redirection côté serveur pour transformation
                        window.location.href = `?lang=${currentLang}&section=${newSection}`;
                    }
                });
            });
            
            // Ajouter une classe pour indiquer que JavaScript est activé
            document.body.classList.add('js-enabled');
        });
    </script>
    
    <!-- Métadonnées de debug pour les robots -->
    <!-- 
    Transformation effectuée côté serveur avec PHP
    Langue: <?php echo $lang; ?>
    Section: <?php echo $section; ?>
    Fichier XML source: content.xml
    Fichier XSL: content.xsl
    -->
</body>
</html>
    <?php
    
} catch (Exception $e) {
    // Gestion d'erreur
    http_response_code(500);
    ?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Erreur - Portfolio</title>
    <meta name="robots" content="noindex">
</head>
<body>
    <h1>Erreur de transformation XML/XSL</h1>
    <p>Une erreur s'est produite lors de la génération de la page :</p>
    <p><strong><?php echo htmlspecialchars($e->getMessage()); ?></strong></p>
    <p><a href="?lang=fr&section=accueil">Retour à l'accueil</a></p>
</body>
</html>
    <?php
}
?>