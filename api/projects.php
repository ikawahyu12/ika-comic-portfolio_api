<?php

header('Content-Type: application/json; charset=UTF-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

require_once __DIR__ . '/../config/database.php';

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {

    // =========================
    // GET
    // =========================
    case 'GET':

        try {
            $stmt = $pdo->query(
                "SELECT * FROM projects ORDER BY id DESC"
            );

            $projects = $stmt->fetchAll();

            echo json_encode([
                'success' => true,
                'data' => $projects
            ]);

        } catch (PDOException $e) {

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => 'Gagal mengambil data',
                'error' => $e->getMessage()
            ]);
        }

        break;


    // =========================
    // POST
    // =========================
    case 'POST':

        $input = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (
            empty($input['title']) ||
            empty($input['description']) ||
            empty($input['category']) ||
            empty($input['technologies'])
        ) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'Data wajib belum lengkap'
            ]);

            exit;
        }

        try {

            $stmt = $pdo->prepare(
                "INSERT INTO projects
                (title, description, category, technologies, image, github_url)
                VALUES
                (:title, :description, :category, :technologies, :image, :github_url)"
            );

            $stmt->execute([
                ':title' => $input['title'],
                ':description' => $input['description'],
                ':category' => $input['category'],
                ':technologies' => $input['technologies'],
                ':image' => $input['image'] ?? null,
                ':github_url' => $input['github_url'] ?? null,
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Portfolio berhasil ditambahkan',
                'id' => $pdo->lastInsertId()
            ]);

        } catch (PDOException $e) {

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => 'Gagal menambahkan portfolio',
                'error' => $e->getMessage()
            ]);
        }

        break;


    // =========================
    // PUT
    // =========================
    case 'PUT':

        $input = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (empty($input['id'])) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'ID portfolio wajib diisi'
            ]);

            exit;
        }

        try {

            $stmt = $pdo->prepare(
                "UPDATE projects
                SET
                    title = :title,
                    description = :description,
                    category = :category,
                    technologies = :technologies,
                    image = :image,
                    github_url = :github_url
                WHERE id = :id"
            );

            $stmt->execute([
                ':id' => $input['id'],
                ':title' => $input['title'],
                ':description' => $input['description'],
                ':category' => $input['category'],
                ':technologies' => $input['technologies'],
                ':image' => $input['image'] ?? null,
                ':github_url' => $input['github_url'] ?? null,
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Portfolio berhasil diperbarui'
            ]);

        } catch (PDOException $e) {

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => 'Gagal memperbarui portfolio',
                'error' => $e->getMessage()
            ]);
        }

        break;


    // =========================
    // DELETE
    // =========================
    case 'DELETE':

        $input = json_decode(
            file_get_contents('php://input'),
            true
        );

        if (empty($input['id'])) {
            http_response_code(400);

            echo json_encode([
                'success' => false,
                'message' => 'ID portfolio wajib diisi'
            ]);

            exit;
        }

        try {

            $stmt = $pdo->prepare(
                "DELETE FROM projects WHERE id = :id"
            );

            $stmt->execute([
                ':id' => $input['id']
            ]);

            echo json_encode([
                'success' => true,
                'message' => 'Portfolio berhasil dihapus'
            ]);

        } catch (PDOException $e) {

            http_response_code(500);

            echo json_encode([
                'success' => false,
                'message' => 'Gagal menghapus portfolio',
                'error' => $e->getMessage()
            ]);
        }

        break;


    // =========================
    // METHOD LAIN
    // =========================
    default:

        http_response_code(405);

        echo json_encode([
            'success' => false,
            'message' => 'Method tidak didukung'
        ]);

        break;
}