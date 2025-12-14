<?php
// models/Model.php
class Model {
    protected $db;

    public function __construct() {
        // FIX ĐƯỜNG DẪN – ĐÚNG 100% CHO DỰ ÁN CỦA BẠN
        require_once dirname(__DIR__) . '/config/ConnectDb.php';
        
        // Lấy kết nối từ class ConnectDb của nhóm bạn
        $connect = new ConnectDb();
        $this->db = $connect->getConnection();
    }
}
?>