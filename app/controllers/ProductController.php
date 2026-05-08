<?php
class ProductController
{
    public function adminList()
    {
        ensureAdmin();
        $products = Product::getAll();
        require APP_ROOT . '/app/views/admin/products.php';
    }

    public function adminForm()
    {
        ensureAdmin();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        $product = $id ? Product::getById($id) : null;

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $data = [
                'restaurant_id' => (int) ($_POST['restaurant_id'] ?? 0),
                'name' => trim($_POST['name'] ?? ''),
                'category' => trim($_POST['category'] ?? ''),
                'display_category' => trim($_POST['display_category'] ?? ''),
                'price' => (float) ($_POST['price'] ?? 0),
                'unit' => trim($_POST['unit'] ?? ''),
                'description' => trim($_POST['description'] ?? ''),
                'badge' => trim($_POST['badge'] ?? ''),
                'emoji' => trim($_POST['emoji'] ?? ''),
            ];

            if ($id) {
                Product::update($id, $data);
            } else {
                Product::create($data);
            }
            redirect('admin_products');
        }

        require APP_ROOT . '/app/views/admin/product_form.php';
    }

    public function adminDelete()
    {
        ensureAdmin();
        $id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
        if ($id) {
            Product::delete($id);
        }
        redirect('admin_products');
    }
}
