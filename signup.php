<?php
require_once('app/functions.php');
$nationalities = getNationalities();
$sports = getAllSports();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $firstName = trim($_POST['firstName'] ?? '');
    $lastName = trim($_POST['lastName'] ?? '');
    $gender = trim($_POST['gender'] ?? '');
    $nationalityId = trim($_POST['nationality'] ?? '');
    $sportIds = $_POST['sports'] ?? [];
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirmPassword'] ?? '';

    // Check if passwords match
    if ($password !== $confirmPassword) {
        $error_message = "Les mots de passe ne correspondent pas";
    } else {
        $result = signup($email, $firstName, $lastName, $gender, $nationalityId, $sportIds, $password);
        if ($result) {
            // Redirect to the login page after successful signup
            header("Location: login.php");
            exit();
        } else {
            $error_message = "Erreur lors de l'inscription. Veuillez réessayer.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">

<head>
    <meta charset="UTF-8">
    <title>Inscription</title>
</head>

<body>
    <h2>Inscription</h2>
    <?php if (isset($error_message)) echo "<p>$error_message</p>"; ?>
    <form action="<?php echo htmlspecialchars($_SERVER['PHP_SELF']); ?>" method="post">
        <label for="firstName">Prénom:</label><br>
        <input type="text" id="firstName" name="firstName" required><br><br>
        <label for="lastName">Nom:</label><br>
        <input type="text" id="lastName" name="lastName" required><br><br>
        <label for="gender">Sexe:</label><br>
        <select id="gender" name="gender" required>
            <option value="Male">Homme</option>
            <option value="Female">Femme</option>
            <option value="Other">Autre</option>
        </select><br><br>
        <label for="nationality">Nationalité:</label><br>
        <select id="nationality" name="nationality" required>
            <?php foreach ($nationalities as $nat) : ?>
                <option value="<?php echo htmlspecialchars($nat['id']); ?>">
                    <?php echo htmlspecialchars($nat['country_name']); ?>
                </option>
            <?php endforeach; ?>
        </select><br><br>
        <label for="sports">Sport favori:</label><br>
        <select id="sports" name="sports[]" multiple>
            <?php foreach ($sports as $sport) : ?>
                <option value="<?php echo $sport['id']; ?>"><?php echo $sport['name']; ?></option>
            <?php endforeach; ?>
        </select><br><br>
        <label for="email">Email:</label><br>
        <input type="email" id="email" name="email" required><br><br>
        <label for="password">Mot de passe:</label><br>
        <input type="password" id="password" name="password" required><br><br>
        <label for="confirmPassword">Confirmer le mot de passe:</label><br>
        <input type="password" id="confirmPassword" name="confirmPassword" required><br><br>
        <input type="submit" value="Inscription">
    </form>
    <p>Vous avez déjà un compte? <a href="login.php">Connectez-vous ici</a>.</p>
</body>

</html>