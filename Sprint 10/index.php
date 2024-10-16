<?php
session_start();
include('db.php');

// Inicializa variáveis
$pressao = "";
$batimentos = "";
$medicamentos = [];

// Verifica se o formulário de monitoramento foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['monitoramento'])) {
    $pressao = $_POST['pressao'];
    $batimentos = $_POST['batimentos'];

    $stmt = $conn->prepare("INSERT INTO monitoramento (usuario_id, pressao, batimentos) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $_SESSION['usuario_id'], $pressao, $batimentos);
    $stmt->execute();
    $stmt->close();
}

// Verifica se o formulário de agendamento de medicamentos foi enviado
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['agendamento'])) {
    $medicamento = $_POST['medicamento'];
    $data = $_POST['data'];
    $hora = $_POST['hora'];

    $stmt = $conn->prepare("INSERT INTO medicamentos (usuario_id, nome, data, hora) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("isss", $_SESSION['usuario_id'], $medicamento, $data, $hora);
    $stmt->execute();
    $stmt->close();
}

// Recupera os dados de medicamentos do banco
$stmt = $conn->prepare("SELECT nome, data, hora FROM medicamentos WHERE usuario_id = ?");
$stmt->bind_param("i", $_SESSION['usuario_id']);
$stmt->execute();
$result = $stmt->get_result();

while ($row = $result->fetch_assoc()) {
    $medicamentos[] = $row;
}
$stmt->close();
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel de Monitoramento</title>
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
    <h2>Painel Principal</h2>
    <p>Bem-vindo, <?php echo htmlspecialchars($_SESSION['usuario']); ?>!</p>

    <div class="sub-nav">
        <a href="#monitoramento">Monitoramento</a>
        <a href="#agendamento">Agendamento de Medicamentos</a>
        <a href="#historico">Histórico</a>
    </div>

    <div id="monitoramento" class="tab-content">
        <h3>Monitoramento</h3>
        <form method="POST" class="form">
            <label for="pressao">Pressão Arterial:</label>
            <input type="text" name="pressao" id="pressao" value="<?php echo htmlspecialchars($pressao); ?>" required>

            <label for="batimentos">Batimentos Cardíacos:</label>
            <input type="text" name="batimentos" id="batimentos" value="<?php echo htmlspecialchars($batimentos); ?>" required>

            <input type="submit" name="monitoramento" value="Salvar">
        </form>
        <h4>Dados Salvos:</h4>
        <p>Pressão Arterial: <?php echo htmlspecialchars($pressao); ?></p>
        <p>Batimentos Cardíacos: <?php echo htmlspecialchars($batimentos); ?></p>
    </div>

    <div id="agendamento" class="tab-content">
        <h3>Agendamento de Medicamentos</h3>
        <form method="POST" class="form">
            <label for="medicamento">Nome do Medicamento:</label>
            <input type="text" name="medicamento" id="medicamento" required>

            <label for="data">Data:</label>
            <input type="date" name="data" id="data" required>

            <label for="hora">Hora:</label>
            <input type="time" name="hora" id="hora" required>

            <input type="submit" name="agendamento" value="Agendar">
        </form>
        <h4>Medicamentos Agendados:</h4>
        <ul class="medicamentos-list">
            <?php foreach ($medicamentos as $med) : ?>
                <li><?php echo htmlspecialchars($med['nome']) . " - " . htmlspecialchars($med['data']) . " às " . htmlspecialchars($med['hora']); ?></li>
            <?php endforeach; ?>
        </ul>
    </div>

    <div id="historico" class="tab-content">
        <h3>Histórico</h3>
        <ul class="medicamentos-list">
            <?php if (empty($medicamentos)) : ?>
                <li>Nenhum agendamento encontrado.</li>
            <?php else : ?>
                <?php foreach ($medicamentos as $med) : ?>
                    <li><?php echo htmlspecialchars($med['nome']) . " - " . htmlspecialchars($med['data']) . " às " . htmlspecialchars($med['hora']); ?></li>
                <?php endforeach; ?>
            <?php endif; ?>
        </ul>
    </div>
</div>

</body>
</html>
