<?php
include('db.php');
session_start();

if (isset($_SESSION['usuario'])) {
    header('Location: index.php');
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST['new-username'];
    $email = $_POST['email'];
    $senha = password_hash($_POST['new-password'], PASSWORD_BCRYPT);

    $sql = "INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sss", $nome, $email, $senha);

    if ($stmt->execute()) {
        header('Location: login.php');
    } else {
        echo "<p style='color: red;'>Erro ao registrar o usuário: " . $stmt->error . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
    <div class="register-container">
        <h2>Registrar</h2>
        <form action="register.php" method="post">
            <label for="new-username">Nome:</label>
            <input type="text" id="new-username" name="new-username" required>
            <label for="email">Email:</label>
            <input type="email" id="email" name="email" required>
            <label for="new-password">Senha:</label>
            <input type="password" id="new-password" name="new-password" required>
            <input type="submit" value="Registrar">
        </form>
        <p>Já tem uma conta? <a href="login.php">Faça login aqui</a></p>
    </div>
</body>
</html>
