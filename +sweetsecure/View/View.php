<?php
// PHP and MySQL Project
// output related stuff

class View
{
    public $companyName = 'Sweets Complete';
    public $page = 'home';
    public $menuPages = [];
    // menus = page => array('page' => 'label')
    // list of menu choices on each page
    public $menus = [
        'home' => ['about' => 'About Us',
            'products' => 'Products',
            'specials' => 'Specials',
            'contact' => 'Contact Us'],
        'thanks' => ['about' => 'About Us',
            'products' => 'Products',
            'specials' => 'Specials',
            'contact' => 'Contact Us'],
        'about' => ['home' => 'Home',
            'products' => 'Products',
            'specials' => 'Specials',
            'contact' => 'Contact Us'],
        'products' => ['home' => 'Home',
            'about' => 'About Us',
            'specials' => 'Specials',
            'contact' => 'Contact Us'],
        'specials' => ['home' => 'Home',
            'about' => 'About Us',
            'products' => 'Products',
            'contact' => 'Contact Us'],
        'contact' => ['home' => 'Home',
            'about' => 'About Us',
            'products' => 'Products',
            'specials' => 'Specials'],
        'detail' => ['home' => 'Home',
            'about' => 'About Us',
            'products' => 'Products',
            'contact' => 'Contact Us'],
        'search' => ['home' => 'Home',
            'about' => 'About Us',
            'specials' => 'Specials',
            'contact' => 'Contact Us'],
        'purchase' => ['home' => 'Home',
            'about' => 'About Us',
            'products' => 'Products',
            'contact' => 'Contact Us'],
        'cart' => ['home' => 'Home',
            'about' => 'About Us',
            'products' => 'Products',
            'contact' => 'Contact Us'],
        'checkout' => ['home' => 'Home',
            'about' => 'About Us',
            'products' => 'Products',
            'contact' => 'Contact Us'],
        'members' => ['home' => 'Home',
            'products' => 'Products',
            'specials' => 'Specials',
            'contact' => 'Contact Us'],
        'addmember' => ['home' => 'Home',
            'products' => 'Products',
            'specials' => 'Specials',
            'contact' => 'Contact Us'],
        // *** should add a "logout" menu option!
        'login' => ['home' => 'Home',
            'products' => 'Products',
            'specials' => 'Specials',
            'contact' => 'Contact Us'],
        'admin' => ['home' => 'Home',
            'products' => 'Products',
            'specials' => 'Specials',
            'contact' => 'Contact Us'],
        'change' => ['home' => 'Home',
            'products' => 'Products',
            'specials' => 'Specials',
            'contact' => 'Contact Us'],
        'confirm' => ['home' => 'Home',
            'products' => 'Products',
            'specials' => 'Specials',
            'contact' => 'Contact Us'],
        'history' => ['home' => 'Home',
            'about' => 'About Us',
            'products' => 'Products',
            'contact' => 'Contact Us'],
        'top' => ['login' => 'Login',
            'addmember' => 'Sign Up',
            'members' => 'Members',
            'cart' => 'Shopping Cart'],
    ];
    
    public function __construct() {
        $this->menuPages = array_keys($this->menus);
    }
    
    /*
     * Produces a search form
     * @param array $titles = array[product_id] = title [optional]
     */
    public function searchForm($titles = null) {
        $output = '<form name="search" method="get" action="?page=search" id="search">' . PHP_EOL;
        $output .= '<input type="text" value="keywords" name="keyword" class="s0" />' . PHP_EOL;
        if($titles) {
            $output .= '<br />' . PHP_EOL;
            $output .= '<select name="title" class="s2">' . PHP_EOL;
            foreach($titles as $id => $title) {
                $output .= sprintf('<option value="%s">%s</option>', $id, $title);
            }
            $output .= '</select>' . PHP_EOL;
            $output .= '<br />' . PHP_EOL;
        }
        $output .= '<input type="submit" name="search" value="Search Products" class="button marT5" />' . PHP_EOL;
        $output .= '<input type="hidden" name="page" value="search" />' . PHP_EOL;
        $output .= '</form><br /><br />' . PHP_EOL;
        return $output;
    }
    
    /*
     * Displays products as a series of <li> tags
     * @param array $products
     * @return string $output = HTML output
     */
    public function displayProducts($products) {
        $output = '';
        foreach($products as $row) {
            $link = '?page=detail&id=' . $row['product_id'];
            $output .= '<li>' . PHP_EOL;
            $output .= '<div class="image">' . PHP_EOL;
            $output .= '<a href="<?php echo $link; ?>">' . PHP_EOL;
            $output .= '<img src="images/' . $row['link'] . '.scale_20.JPG" alt="' . $row['title'] . '" width="190" height="130"/>' . PHP_EOL;
            $output .= '</a>' . PHP_EOL;
            $output .= '</div>' . PHP_EOL;
            $output .= '<div class="detail">' . PHP_EOL;
            $output .= '<p class="name">' . PHP_EOL;
            $output .= '<a href="' . $link . '">' . $row['title'] . '</a></p>' . PHP_EOL;
            $output .= '<p class="view">' . PHP_EOL;
            $output .= '<a href="' . $link . '">purchase</a> | <a href="' . $link . '">view details >></a></p>' . PHP_EOL;
            $output .= '</div>' . PHP_EOL;
            $output .= '</li>' . PHP_EOL;
        } // foreach ($products as $row)
        return $output;
    }
}
