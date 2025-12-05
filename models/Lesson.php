<?php

class Lesson
{
    private $conn;
    private $tableName = "lessons";

    public $id;
    public $courseId;
    public $title;
    public $content;
    public $videoUrl;
    public $lessonOrder;
    public $createdAt;
    public $updatedAt;

    public function __construct($db)
    {
        $this->conn = $db;
    }

    public function create()
    {
        $query = "INSERT INTO " . $this->tableName . "
                SET course_id = :course_id,
                    title = :title,
                    content = :content,
                    video_url = :video_url,
                    lesson_order = :lesson_order,
                    created_at = NOW()";

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->content = $this->content ? htmlspecialchars($this->content) : null;
        $this->videoUrl = $this->videoUrl ? htmlspecialchars(strip_tags($this->videoUrl)) : null;

        $stmt->bindParam(":course_id", $this->courseId);
        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":video_url", $this->videoUrl);
        $stmt->bindParam(":lesson_order", $this->lessonOrder);

        if ($stmt->execute()) {
            $this->id = $this->conn->lastInsertId();
            return $this->id;
        }

        return false;
    }

    public function update()
    {
        $query = "UPDATE " . $this->tableName . "
                SET title = :title,
                    content = :content,
                    video_url = :video_url,
                    lesson_order = :lesson_order,
                    updated_at = NOW()
                WHERE id = :id";

        $stmt = $this->conn->prepare($query);

        $this->title = htmlspecialchars(strip_tags($this->title));
        $this->content = $this->content ? htmlspecialchars($this->content) : null;
        $this->videoUrl = $this->videoUrl ? htmlspecialchars(strip_tags($this->videoUrl)) : null;

        $stmt->bindParam(":title", $this->title);
        $stmt->bindParam(":content", $this->content);
        $stmt->bindParam(":video_url", $this->videoUrl);
        $stmt->bindParam(":lesson_order", $this->lessonOrder);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    public function delete()
    {
        $query = "DELETE FROM " . $this->tableName . " WHERE id = :id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":id", $this->id);

        return $stmt->execute();
    }

    public function getByCourse($courseId)
    {
        $query = "SELECT * FROM " . $this->tableName . "
                WHERE course_id = :course_id
                ORDER BY lesson_order ASC";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function readOne($id)
    {
        $query = "SELECT l.*, c.title as course_title, c.instructor_id
                FROM " . $this->tableName . " l
                LEFT JOIN courses c ON l.course_id = c.id
                WHERE l.id = :id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $row = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $this->id = $row['id'];
            $this->courseId = $row['course_id'];
            $this->title = $row['title'];
            $this->content = $row['content'];
            $this->videoUrl = $row['video_url'];
            $this->lessonOrder = $row['lesson_order'];
            $this->createdAt = $row['created_at'];
            $this->updatedAt = $row['updated_at'];
            
            return $row;
        }
        
        return false;
    }

    public function isLessonOwner($lessonId, $instructorId)
    {
        $query = "SELECT l.id 
                FROM " . $this->tableName . " l
                LEFT JOIN courses c ON l.course_id = c.id
                WHERE l.id = :lesson_id AND c.instructor_id = :instructor_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':lesson_id', $lessonId, PDO::PARAM_INT);
        $stmt->bindValue(':instructor_id', $instructorId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->rowCount() > 0;
    }

    public function countByCourse($courseId)
    {
        $query = "SELECT COUNT(*) as count FROM " . $this->tableName . " 
                WHERE course_id = :course_id";

        $stmt = $this->conn->prepare($query);
        $stmt->bindValue(':course_id', $courseId, PDO::PARAM_INT);
        $stmt->execute();

        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return (int)$result['count'];
    }
}