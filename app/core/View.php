<?php
/**
 * Royal Education Center Management System
 * View Class
 */

class View {
    private $viewPath;
    private $layoutPath;
    
    public function __construct() {
        $this->viewPath = APP_PATH . '/views/';
        $this->layoutPath = $this->viewPath . 'layouts/';
    }
    
    // Render view without layout
    public function render($viewName, $data = []) {
        // Extract data to variables
        extract($data);
        
        // Convert view name to path (e.g., 'admin/dashboard' -> 'admin/dashboard.php')
        $viewFile = $this->viewPath . str_replace('.', '/', $viewName) . '.php';
        
        if (!file_exists($viewFile)) {
            throw new Exception("View file not found: $viewFile");
        }
        
        // Start output buffering
        ob_start();
        include $viewFile;
        $content = ob_get_clean();
        
        echo $content;
    }
    
    // Render view with layout
    public function renderLayout($layoutName, $viewName, $data = []) {
        // Extract data to variables
        extract($data);
        
        // Convert view name to path
        $viewFile = $this->viewPath . str_replace('.', '/', $viewName) . '.php';
        
        if (!file_exists($viewFile)) {
            throw new Exception("View file not found: $viewFile");
        }
        
        // Start output buffering for the view content
        ob_start();
        include $viewFile;
        $content = ob_get_clean();
        
        // Set content variable for layout
        $data['content'] = $content;
        
        // Extract data again including content
        extract($data);
        
        // Load layout
        $layoutFile = $this->layoutPath . $layoutName . '.php';
        
        if (!file_exists($layoutFile)) {
            throw new Exception("Layout file not found: $layoutFile");
        }
        
        include $layoutFile;
    }
    
    // Include partial view
    public function partial($partialName, $data = []) {
        extract($data);
        $partialFile = $this->viewPath . 'partials/' . $partialName . '.php';
        
        if (file_exists($partialFile)) {
            include $partialFile;
        }
    }
    
    // Escape HTML output
    public function escape($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    // Generate URL
    public function url($path = '') {
        return BASE_URL . '/' . ltrim($path, '/');
    }
    
    // Generate asset URL
    public function asset($path) {
        return ASSETS_URL . '/' . ltrim($path, '/');
    }
}
