<?php

class Material
{
    private $conn;
    private $tableName = "materials";

    public $id;
    public $lessonId;
    public $filename;
    public $filePath;
    public $fileType;
    public $fileSize;
    public $uploadedAt;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->tableName . "
                SET lesson_id = :lesson_id,
                    filename = :filename,
                    file_path = :file_path,
                    file_type = :file_type,
                    file_size = :file_size,
                    uploaded_at = NOW()";

        $stmt = $this->conn->prepare($query);

        $this->filename = htmlspecialchars(strip_tags($this->filename));

        $stmt->bindParam(":lesson_id", $this->lessonId);
        $stmt->bindParam(":filename", $this->filename);
        $stmt->bindParam(":file_path", $this->filePath);
        $stmt->bindParam(":file_type", $this->fileType);
        $stmt->bindParam(":file_size", $this->fileSize);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return $this->id;
        }

        return false;
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->tableName . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    public function getByLesson($lessonId)
    {
        $query = "SELECT * FROM " . $this->tableName . "
                WHERE lesson_id = :lesson_id
                ORDER BY uploaded_at DESC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':lesson_id', $lessonId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readOne($id)
    {
        $query = "SELECT m.*, l.title as lesson_title, l.course_id, c.instructor_id
                FROM " . $this->tableName . " m
                LEFT JOIN lessons l ON m.lesson_id = l.id
                LEFT JOIN courses c ON l.course_id = c.id
                WHERE m.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $row['id'];
            $this->lessonId = $row['lesson_id'];
            $this->filename = $row['filename'];
            $this->filePath = $row['file_path'];
            $this->fileType = $row['file_type'];
            $this->fileSize = $row['file_size'];
            $this->uploadedAt = $row['uploaded_at'];
            
            return $row;
        }
        
        return false;
    }

    public function isMaterialOwner($materialId, $instructorId)
    {
        $query = "SELECT m.id 
                FROM " . $this->tableName . " m
                LEFT JOIN lessons l ON m.lesson_id = l.id
                LEFT JOIN courses c ON l.course_id = c.id
                WHERE m.id = :material_id AND c.instructor_id = :instructor_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':material_id', $materialId, PDO::PARAM_INT);
        $stmt->bindValue(':instructor_id', $instructorId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }
}