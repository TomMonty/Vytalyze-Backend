<?php
require_once(__DIR__ . '/../config/database.php');

function getNationalities()
{
    global $pdo;
    $stmt = $pdo->query('SELECT id, country_name FROM nationalities ORDER BY country_name');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllSports()
{
    global $pdo;
    $stmt = $pdo->query('SELECT id, name FROM sports ORDER BY name');
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getUserIdByEmail($email)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM users WHERE mail = :email");
    $stmt->execute(['email' => $email]);
    return $stmt->fetchColumn();
}

function login($email, $password)
{
    global $pdo;

    $sql = "SELECT * FROM users WHERE mail = :email";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user && password_verify($password, $user['password'])) {
        return $user;
    }
    return false;
}

function signup($email, $firstName, $lastName, $gender, $nationality_id, $sport_ids, $password)
{
    global $pdo;

    $password_hash = password_hash($password, PASSWORD_DEFAULT);

    try {
        $pdo->beginTransaction();

        $sql = "INSERT INTO users (mail, first_name, last_name, gender, nationality_id, password) VALUES (:email, :firstName, :lastName, :gender, :nationality_id, :password)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            'email' => $email,
            'firstName' => $firstName,
            'lastName' => $lastName,
            'gender' => $gender,
            'nationality_id' => $nationality_id,
            'password' => $password_hash
        ]);

        $userId = $pdo->lastInsertId();

        if (!empty($sport_ids)) {
            foreach ($sport_ids as $sport_id) {
                $sql = "INSERT INTO user_sports (user_id, sport_id) VALUES (:user_id, :sport_id)";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([
                    'user_id' => $userId,
                    'sport_id' => $sport_id
                ]);
            }
        }

        $pdo->commit();
        return true;
    } catch (PDOException $e) {
        error_log("Erreur lors de l'inscription: " . $e->getMessage());
        $pdo->rollBack();
        return false;
    }
}

function saveProfileImage($userId, $imageData)
{
    global $pdo;
    $sql = "INSERT INTO user_images (user_id, user_image) VALUES (:user_id, :user_image)";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId, 'user_image' => $imageData]);
}

function getProfileImage($userId)
{
    global $pdo;
    $sql = "SELECT user_image FROM user_images WHERE user_id = :user_id ORDER BY id DESC LIMIT 1";
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetchColumn();
}

function handleProfilePictureUpload($userId)
{
    global $pdo;
    $uploadDirectory = 'profile_pictures/';

    // Check if a file has been uploaded successfully
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] === UPLOAD_ERR_OK) {

        // Get the name and path of the uploaded file
        $fileName = $_FILES['profile_picture']['name'];
        $tempFilePath = $_FILES['profile_picture']['tmp_name'];

        // Generate a unique file name to avoid name conflicts
        $newFileName = uniqid('profile_') . '_' . $fileName;
        $uploadFilePath = $uploadDirectory . $newFileName;

        // Move the uploaded file to the destination folder
        if (move_uploaded_file($tempFilePath, $uploadFilePath)) {
            // Update the image path in the database
            $sql = "INSERT INTO user_images (user_id, user_image) VALUES (:user_id, :user_image)
                    ON DUPLICATE KEY UPDATE user_image = :user_image";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['user_id' => $userId, 'user_image' => $uploadFilePath]);

            // Redirect to the profile page
            header("Location: profile.php");
            exit();
        } else {
            echo "Error moving uploaded file.";
        }
    } else {
        echo "Error uploading profile image.";
    }
}

function showAddMediaForm()
{
    echo "<div>";
    echo "<h2>Ajouter un média</h2>";
    echo "<form action='profile.php' method='post' enctype='multipart/form-data'>";
    echo "<label for='media'>Sélectionner un média:</label>";
    echo "<input type='file' name='media' id='media'>";
    echo "<input type='submit' value='Télécharger'>";
    echo "</form>";
    echo "</div>";
}

function handleMediaUpload($userId)
{
    global $pdo;
    if ($_FILES['media']['error'] === UPLOAD_ERR_OK) {
        $fileType = mime_content_type($_FILES['media']['tmp_name']);
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4'];
        if (!in_array($fileType, $allowedTypes)) {
            echo "Type de fichier invalide.";
            return;
        }

        $fileName = uniqid('media_') . '_' . basename($_FILES['media']['name']);
        $uploadFilePath = 'media/' . $fileName;

        if (move_uploaded_file($_FILES['media']['tmp_name'], $uploadFilePath)) {
            $stmt = $pdo->prepare("INSERT INTO user_media (user_id, media_path) VALUES (:user_id, :media_path)");
            $stmt->execute(['user_id' => $userId, 'media_path' => $fileName]);
            echo "Média téléchargé avec succès.";
        } else {
            echo "Erreur lors du déplacement du fichier téléchargé.";
        }
    } else {
        echo "Erreur lors du téléchargement du média.";
    }
}

function updateFavoriteSports($userId, $sports)
{
    global $pdo;
    $stmt = $pdo->prepare("DELETE FROM user_sports WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $userId]);

    foreach ($sports as $sport_id) {
        $stmt = $pdo->prepare("INSERT INTO user_sports (user_id, sport_id) VALUES (:user_id, :sport_id)");
        $stmt->execute(['user_id' => $userId, 'sport_id' => $sport_id]);
    }
}

function getSelectedSports($userId)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT sport_id FROM user_sports WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_COLUMN);
}


function handlePostUpload($userId)
{
    global $pdo;
    $uploadDirectory = 'post_photos/';

    // Check if a file has been uploaded successfully
    if (isset($_FILES['post_image']) && $_FILES['post_image']['error'] === UPLOAD_ERR_OK) {
        // Get the name and path of the uploaded file
        $fileName = $_FILES['post_image']['name'];
        $tempFilePath = $_FILES['post_image']['tmp_name'];

        // Generate a unique file name to avoid name conflicts
        $newFileName = uniqid('post_photo_') . '_' . $fileName;
        $uploadFilePath = $uploadDirectory . $newFileName;

        // Move the uploaded file to the destination folder
        if (move_uploaded_file($tempFilePath, $uploadFilePath)) {
            // Get title and description from the form
            $title = $_POST['post_title'];
            $description = $_POST['post_description'];

            // Insert post details into the database
            $sql = "INSERT INTO posts (user_id, title, post_photo, description) VALUES (:user_id, :title, :post_photo, :description)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['user_id' => $userId, 'title' => $title, 'post_photo' => $uploadFilePath, 'description' => $description]);

            // Redirect to the profile page
            header("Location: profile.php");
            exit();
        } else {
            echo "Error moving uploaded file.";
        }
    } else {
        echo "Error uploading photo.";
    }
}

function getUserInfo($userId)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT first_name, last_name FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getUploadedPosts($userId)
{
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM posts WHERE user_id = :user_id");
    $stmt->execute(['user_id' => $userId]);
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
