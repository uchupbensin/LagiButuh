<?php
class CacheService {
    private $db;
    private $cacheDir;
    
    public function __construct($db) {
        $this->db = $db;
        $this->cacheDir = __DIR__.'/../../cache/';
        
        if (!file_exists($this->cacheDir)) {
            mkdir($this->cacheDir, 0755, true);
        }
    }
    
    public function get($key, $callback = null, $ttl = 3600) {
        $cacheFile = $this->cacheDir . md5($key) . '.cache';
        
        // Check if cache exists and is still valid
        if (file_exists($cacheFile) {
            $data = unserialize(file_get_contents($cacheFile));
            
            if (time() - $data['timestamp'] < $ttl) {
                return $data['content'];
            }
        }
        
        // If no cache or expired, execute callback and store result
        if (is_callable($callback)) {
            $content = call_user_func($callback);
            $this->set($key, $content);
            return $content;
        }
        
        return null;
    }
    
    public function set($key, $content) {
        $cacheFile = $this->cacheDir . md5($key) . '.cache';
        
        $data = [
            'timestamp' => time(),
            'content' => $content
        ];
        
        file_put_contents($cacheFile, serialize($data));
    }
    
    public function delete($key) {
        $cacheFile = $this->cacheDir . md5($key) . '.cache';
        
        if (file_exists($cacheFile)) {
            unlink($cacheFile);
            return true;
        }
        
        return false;
    }
    
    public function clearAll() {
        $files = glob($this->cacheDir . '*.cache');
        
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
        
        return count($files);
    }
}
?>