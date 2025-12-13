<?php
// Canonical Course model merged. Require the project's Database helper from config.
require_once __DIR__ . '/../config/Database.php';

class Course {
    private $pdo;
    private $table = 'courses';

    public function __construct() {
        $db = new Database();
        $this->pdo = $db->getConnection();
    }

    // Flexible listing: keyword search + optional category filter
    public function getAll($keyword = '', $category_id = null) {
        try {
            $sql = "SELECT c.*, cat.name AS category_name, u.fullname AS instructor_name
                    FROM {$this->table} c
                    LEFT JOIN categories cat ON c.category_id = cat.id
                    LEFT JOIN users u ON c.instructor_id = u.id
                    WHERE 1=1";

            $params = [];
            if ($keyword !== null && $keyword !== '') {
                $sql .= " AND c.title LIKE :keyword";
                $params[':keyword'] = "%$keyword%";
            }
            if ($category_id !== null && $category_id !== '') {
                $sql .= " AND c.category_id = :category_id";
                $params[':category_id'] = $category_id;
            }

            $sql .= " ORDER BY c.created_at DESC";

            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Course::getAll error: ' . $e->getMessage());
            return [];
        }
    }

    public function getById($id) {
        try {
            $sql = "SELECT c.*, cat.name AS category_name, u.fullname AS instructor_name
                    FROM {$this->table} c
                    LEFT JOIN categories cat ON c.category_id = cat.id
                    LEFT JOIN users u ON c.instructor_id = u.id
                    WHERE c.id = :id LIMIT 1";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([':id' => (int)$id]);
            return $stmt->fetch(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Course::getById error: ' . $e->getMessage());
            return null;
        }
    }

    public function getByStatus($status) {
        try {
            $stmt = $this->pdo->prepare("SELECT * FROM {$this->table} WHERE status = :status ORDER BY created_at DESC");
            $stmt->execute([':status' => $status]);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Course::getByStatus error: ' . $e->getMessage());
            return [];
        }
    }

    public function getByStatuses(array $statuses) {
        if (empty($statuses)) return [];
        try {
            $placeholders = [];
            $params = [];
            foreach ($statuses as $i => $s) {
                $k = ':s' . $i;
                $placeholders[] = $k;
                $params[$k] = $s;
            }
            $in = implode(',', $placeholders);
            $sql = "SELECT * FROM {$this->table} WHERE status IN ($in) ORDER BY created_at DESC";
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Course::getByStatuses error: ' . $e->getMessage());
            return [];
        }
    }

    public function getByInstructor($instructor_id, $limit = 10, $offset = 0) {
        try {
            $sql = "SELECT c.*, cat.name AS category_name,
                       (SELECT COUNT(DISTINCT student_id) FROM enrollments WHERE course_id = c.id) AS student_count
                    FROM {$this->table} c
                    LEFT JOIN categories cat ON c.category_id = cat.id
                    WHERE c.instructor_id = :instructor_id
                    ORDER BY c.created_at DESC
                    LIMIT :limit OFFSET :offset";
            $stmt = $this->pdo->prepare($sql);
            $stmt->bindValue(':instructor_id', (int)$instructor_id, PDO::PARAM_INT);
            $stmt->bindValue(':limit', (int)$limit, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            error_log('Course::getByInstructor error: ' . $e->getMessage());
            return [];
        }
    }

    public function countByInstructor($instructor_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE instructor_id = :instructor_id");
            $stmt->execute([':instructor_id' => (int)$instructor_id]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            return (int)($res['total'] ?? 0);
        } catch (PDOException $e) {
            error_log('Course::countByInstructor error: ' . $e->getMessage());
            return 0;
        }
    }

    public function getInstructorStats($instructor_id) {
        $stats = ['total_courses' => 0, 'published_courses' => 0, 'pending_courses' => 0, 'total_students' => 0];
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) as total FROM {$this->table} WHERE instructor_id = :instructor_id");
            $stmt->execute([':instructor_id' => (int)$instructor_id]);
            $stats['total_courses'] = (int)($stmt->fetchColumn() ?? 0);

            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table} WHERE instructor_id = :instructor_id AND status = 'published'");
            $stmt->execute([':instructor_id' => (int)$instructor_id]);
            $stats['published_courses'] = (int)($stmt->fetchColumn() ?? 0);

            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM {$this->table} WHERE instructor_id = :instructor_id AND status = 'pending'");
            $stmt->execute([':instructor_id' => (int)$instructor_id]);
            $stats['pending_courses'] = (int)($stmt->fetchColumn() ?? 0);

            $stmt = $this->pdo->prepare("SELECT COUNT(DISTINCT e.student_id) as total_students
                                           FROM enrollments e
                                           INNER JOIN {$this->table} c ON e.course_id = c.id
                                           WHERE c.instructor_id = :instructor_id");
            $stmt->execute([':instructor_id' => (int)$instructor_id]);
            $res = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_students'] = (int)($res['total_students'] ?? 0);
        } catch (PDOException $e) {
            error_log('Course::getInstructorStats error: ' . $e->getMessage());
        }
        return $stats;
    }

    public function countEnrollments($course_id) {
        try {
            $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM enrollments WHERE course_id = :course_id AND status = 'active'");
            $stmt->execute([':course_id' => (int)$course_id]);
            return (int)$stmt->fetchColumn();
        } catch (PDOException $e) {
            error_log('Course::countEnrollments error: ' . $e->getMessage());
            return 0;
        }
    }

    public function create($data) {
        try {
            $sql = "INSERT INTO {$this->table} (title, description, instructor_id, category_id, price, duration_weeks, level, image, status, created_at)
                    VALUES (:title, :description, :instructor_id, :category_id, :price, :duration_weeks, :level, :image, :status, NOW())";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':title' => $data['title'],
                ':description' => $data['description'] ?? '',
                ':instructor_id' => (int)$data['instructor_id'],
                ':category_id' => $data['category_id'] ?? null,
                ':price' => $data['price'] ?? 0,
                ':duration_weeks' => $data['duration_weeks'] ?? 0,
                ':level' => $data['level'] ?? '',
                ':image' => $data['image'] ?? '',
                ':status' => $data['status'] ?? 'pending'
            ]);
        } catch (PDOException $e) {
            error_log('Course::create error: ' . $e->getMessage());
            return false;
        }
    }

    public function update($id, $data) {
        try {
            $sql = "UPDATE {$this->table} SET title = :title, description = :description, category_id = :category_id, price = :price, duration_weeks = :duration_weeks, level = :level, image = :image, status = :status, updated_at = NOW() WHERE id = :id";
            $stmt = $this->pdo->prepare($sql);
            return $stmt->execute([
                ':title' => $data['title'],
                ':description' => $data['description'] ?? '',
                ':category_id' => $data['category_id'] ?? null,
                ':price' => $data['price'] ?? 0,
                ':duration_weeks' => $data['duration_weeks'] ?? 0,
                ':level' => $data['level'] ?? '',
                ':image' => $data['image'] ?? '',
                ':status' => $data['status'] ?? 'pending',
                ':id' => (int)$id
            ]);
        } catch (PDOException $e) {
            error_log('Course::update error: ' . $e->getMessage());
            return false;
        }
    }

    public function setStatus($id, $status) {
        try {
            $stmt = $this->pdo->prepare("UPDATE {$this->table} SET status = :status, updated_at = NOW() WHERE id = :id");
            return $stmt->execute([':status' => $status, ':id' => (int)$id]);
        } catch (PDOException $e) {
            error_log('Course::setStatus error: ' . $e->getMessage());
            return false;
        }
    }

    public function delete($id) {
        try {
            $stmt = $this->pdo->prepare("DELETE FROM {$this->table} WHERE id = :id");
            return $stmt->execute([':id' => (int)$id]);
        } catch (PDOException $e) {
            error_log('Course::delete error: ' . $e->getMessage());
            return false;
        }
    }
}

?>
