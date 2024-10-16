<?php
session_start();
include('db.php');


if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['comment'])) {
    $postagem_id = $_POST['postagem_id'];
    $usuario_id = $_SESSION['usuario_id']; 
    $comentario = $_POST['comentario'];

    $stmt = $conn->prepare("INSERT INTO comentarios (postagem_id, usuario_id, comentario) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $postagem_id, $usuario_id, $comentario);
    $stmt->execute();
    $stmt->close();
}


$stmt = $conn->prepare("SELECT * FROM postagens ORDER BY data DESC");
$stmt->execute();
$result = $stmt->get_result();

$postagens = [];
while ($row = $result->fetch_assoc()) {
    $postagens[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>

<div class="navbar">
    <a href="index.php">Painel Principal</a>
    <a href="quem-somos.php">Quem Somos</a>
    <a href="blog.php">Blog</a>
    <a href="logout.php">Sair</a>
</div>

<div class="content">
    <h2>Blog</h2>

    <?php foreach ($postagens as $postagem) : ?>
        <div class="postagem">
            <h3><?php echo htmlspecialchars($postagem['titulo']); ?></h3>
            <p><?php echo nl2br(htmlspecialchars($postagem['conteudo'])); ?></p>
            <p><em><?php echo htmlspecialchars($postagem['data']); ?></em></p>

            <h4>Comentários:</h4>
            <div class="comentarios">
                <?php
                
                $stmt = $conn->prepare("SELECT * FROM comentarios WHERE postagem_id = ? ORDER BY data DESC");
                $stmt->bind_param("i", $postagem['id']);
                $stmt->execute();
                $comments = $stmt->get_result();

                while ($comment = $comments->fetch_assoc()) {
                    echo "<p>" . htmlspecialchars($comment['comentario']) . " <em> - " . htmlspecialchars($comment['data']) . "</em></p>";
                }
                $stmt->close();
                ?>
            </div>

            <?php if (isset($_SESSION['usuario'])) : ?>
                <form method="POST">
                    <input type="hidden" name="postagem_id" value="<?php echo $postagem['id']; ?>">
                    <label for="comentario">Deixe seu comentário:</label>
                    <textarea name="comentario" required></textarea>
                    <input type="submit" name="comment" value="Comentar">
                </form>
            <?php else : ?>
                <p>Faça login para comentar.</p>
            <?php endif; ?>
        </div>
    <?php endforeach; ?>
</div>

</body>
</html>
