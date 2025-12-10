<?php
require_once 'core/Model.php';

class Course extends Model {
    // Lấy tất cả theo status
    public function getByStatus($status) {
        $query = 'SELECT * FROM courses WHERE status = :status ORDER BY created_at DESC';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy theo nhiều trạng thái (mảng)
    public function getByStatuses(array $statuses) {
        if (empty($statuses)) {
            return [];
        }
        // Build placeholders
        $placeholders = implode(',', array_map(function($i){ return ':s' . $i; }, array_keys($statuses)));
        $query = "SELECT * FROM courses WHERE status IN ($placeholders) ORDER BY created_at DESC";
        $stmt = $this->db->prepare($query);
        foreach ($statuses as $i => $s) {
            $stmt->bindValue(':s' . $i, $s);
        }
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Lấy theo id
    public function getById($id) {
        $query = 'SELECT * FROM courses WHERE id = :id LIMIT 1';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // Cập nhật trạng thái
    public function setStatus($id, $status) {
        $query = 'UPDATE courses SET status = :status, updated_at = NOW() WHERE id = :id';
        $stmt = $this->db->prepare($query);
        $stmt->bindParam(':status', $status);
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }
}

?>
