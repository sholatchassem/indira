
<!-- sidebar.php -->
<div class="sidebar">
    <div class="sidebar-header">
        <link rel="stylesheet" href="style.css">
        <h2>Gestion Stock</h2>
    </div>
    <div class="sidebar-menu">
    <?php
$menuItems = [
    ["section" => "dashboard", "icon" => "📊", "label" => "Dashboard"],
    ["section" => "fournisseurs", "icon" => "🏢", "label" => "Fournisseurs"],
    ["section" => "categories", "icon" => "🏷️", "label" => "Catégories"],
    ["section" => "inventaire", "icon" => "🔍", "label" => "Inventaire"],
    ["section" => "utilisateurs", "icon" => "👤", "label" => "Utilisateurs", "link" => "utilisateurs.php"]
];

foreach ($menuItems as $item) {
    if (isset($item['link'])) {
        // Si un lien est défini, on utilise <a>
        echo "<a class='menu-item' href='{$item['link']}'>
                <i class='menu-icon'>{$item['icon']}</i> {$item['label']}
              </a>";
    } else {
        // Sinon, on garde le <div> avec data-section
        echo "<div class='menu-item' data-section='{$item['section']}'>
                <i class='menu-icon'>{$item['icon']}</i> {$item['label']}
              </div>";
    }
}
?>

        <!-- Sous-menu Produits -->
        <div class="menu-item" data-target="produits-submenu">
            <i class="menu-icon">📦</i> Produits
        </div>
        <div class="submenu" id="produits-submenu">
            <?php
            $subMenuItems = [
                ["section" => "ajouter-produit", "icon" => "➕", "label" => "Ajouter Produit"],
                ["section" => "liste-produits", "icon" => "📋", "label" => "Liste des Produits"],
                ["section" => "entrees-sorties", "icon" => "🔄", "label" => "Entrées/Sorties"]
            ];
            foreach ($subMenuItems as $subItem) {
                echo "<div class='submenu-item' data-section='{$subItem['section']}'><i class='menu-icon'>{$subItem['icon']}</i> {$subItem['label']}</div>";
            }
            ?>
        </div>
    </div>

    <div class="sidebar-footer">
        <div class="user-info">
            <div class="user-avatar">D</div>
            <div class="user-name"><a href="logout.php">Déconnexion</a></div>
        </div>
    </div>
</div>