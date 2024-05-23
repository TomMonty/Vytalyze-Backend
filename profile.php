<?php
session_start();
require_once('app/functions.php');

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$userId = $_SESSION['user_id'];

// Get user's information
$userInfo = getUserInfo($userId);
$firstName = $userInfo['first_name'];
$lastName = $userInfo['last_name'];

// Check if a profile picture has been uploaded and process it if so
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['profile_picture'])) {
    handleProfilePictureUpload($userId);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['post_image'])) {
    handlePostUpload($userId);
}

// Get the current profile image path of the user
$profileImage = getProfileImage($userId);

// Check if a profile image exists for the user
if (!$profileImage || !file_exists($profileImage)) {
    // If no profile image exists, display a default image
    $profileImage = 'default_profile_img/default_profile_img.png';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['sports'])) {
    $selectedSports = $_POST['sports'];
    updateFavoriteSports($userId, $selectedSports);
}

$selectedSports = getSelectedSports($userId);
$sports = getAllSports();

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Profile</title>
    <style>
        .align-div {
            display: flex;
            justify-content: flex-start;
            align-items: center;
            justify-content: space-around;
        }

        .profile-image-container {
            width: 120px;
            height: 120px;
            border-radius: 50%;
            overflow: hidden;
        }

        .post-container {
            border: 1px solid #ccc;
            padding: 10px;
            margin-top: 20px;
            width: calc(33.33% - 20px);
            float: left;
            margin-right: 20px;
            margin-bottom: 20px;
            box-sizing: border-box;
        }

        .post-container:last-child {
            margin-right: 0;
        }

        .post-container h3 {
            margin-top: 0;
        }

        .post-image {
            max-width: 100%;
            height: auto;
        }
    </style>
</head>

<body>
    <p><a href="index.php">Back to Home</a></p>
    <div class="align-div">
        <div>
            <h2><?php echo htmlspecialchars($firstName . ' ' . $lastName); ?></h2>
            <!-- Display the updated profile picture -->
            <div class="profile-image-container">
                <img src="<?php echo htmlspecialchars($profileImage); ?>" alt="Profile Picture" style="width: 100%; height: auto;">
            </div>
        </div>
        <div>
            <!-- Form to upload profile picture -->
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
                <label for="profile_picture">Change Profile Picture:</label><br>
                <input type="file" id="profile_picture" name="profile_picture" accept="image/*"><br><br>
                <input type="submit" value="Upload">
            </form>
        </div>
        <div>
            <!-- Form to save favorite sports -->
            <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
                <label for="sports">Favorite Sports:</label><br>
                <select id="sports" name="sports[]" multiple>
                    <?php foreach ($sports as $sport) : ?>
                        <?php $selected = in_array($sport['id'], $selectedSports) ? 'selected' : ''; ?>
                        <option value="<?php echo htmlspecialchars($sport['id']); ?>" <?php echo $selected; ?>><?php echo htmlspecialchars($sport['name']); ?></option>
                    <?php endforeach; ?>
                </select><br><br>
                <input type="submit" value="Save">
            </form>
        </div>
    </div>

    <!-- New section for uploading posts -->
    <div>
        <h2>Upload Post</h2>
        <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post" enctype="multipart/form-data">
            <label for="post_title">Title:</label><br>
            <input type="text" id="post_title" name="post_title"><br><br>
            <label for="post_image">Image:</label><br>
            <input type="file" id="post_image" name="post_image" accept="image/*"><br><br>
            <label for="post_description">Description:</label><br>
            <textarea id="post_description" name="post_description" rows="4" cols="50"></textarea><br><br>
            <input type="submit" value="Upload">
        </form>
    </div>

    <!-- Display Uploaded Posts Section -->
    <div>
        <h2>Uploaded Posts</h2>
        <?php
        // Retrieve uploaded posts from the database and display them here
        // Example code to fetch and display posts:
        $posts = getUploadedPosts($userId);
        foreach ($posts as $post) {
            echo "<div class='post-container'>";
            echo "<h3>" . htmlspecialchars($post['title']) . "</h3>";
            echo "<img src='" . htmlspecialchars($post['post_photo']) . "' alt='Post Image' class='post-image'>";
            echo "<p>" . htmlspecialchars($post['description']) . "</p>";
            // Check if 'created_at' key exists and if it's not null
            $createdAt = isset($post['created_at']) ? htmlspecialchars($post['created_at']) : '';
            if (!empty($createdAt)) {
                echo "<p>Uploaded on: " . $createdAt . "</p>";
            }
            echo "</div>";
        }
        ?>
    </div>

</body>

</html>