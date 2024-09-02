<?php
require_once "../Loginpage/config.php";
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['id'];
    $segmento = $_POST['segmento'];
    $valor = floatval($_POST['valor']);

    // Obter as emissões atuais do usuário
    $sql = "SELECT * FROM emissoes WHERE user_id = :user_id";
    if ($stmt = $pdo->prepare($sql)) {
        $stmt->bindParam(":user_id", $user_id, PDO::PARAM_INT);
        if ($stmt->execute()) {
            if ($stmt->rowCount() == 1) {
                $row = $stmt->fetch();
                $novo_valor_segmento = $row[$segmento] + $valor;
                $novo_total = $row['total'] + $valor;

                // Atualizar as emissões no banco de dados
                $sql_update = "UPDATE emissoes SET $segmento = :novo_valor_segmento, total = :novo_total WHERE user_id = :user_id";
                if ($stmt_update = $pdo->prepare($sql_update)) {
                    $stmt_update->bindParam(":novo_valor_segmento", $novo_valor_segmento, PDO::PARAM_STR);
                    $stmt_update->bindParam(":novo_total", $novo_total, PDO::PARAM_STR);
                    $stmt_update->bindParam(":user_id", $user_id, PDO::PARAM_INT);
                    if ($stmt_update->execute()) {
                        echo "Emissões atualizadas com sucesso!";
                    } else {
                        echo "Algo deu errado. Tente novamente.";
                    }
                    unset($stmt_update);
                }
            } else {
                // Inserir novo registro se não existir
                $sql_insert = "INSERT INTO emissoes (user_id, $segmento, total) VALUES (:user_id, :valor, :valor)";
                if ($stmt_insert = $pdo->prepare($sql_insert)) {
                    $stmt_insert->bindParam(":user_id", $user_id, PDO::PARAM_INT);
                    $stmt_insert->bindParam(":valor", $valor, PDO::PARAM_STR);
                    if ($stmt_insert->execute()) {
                        echo "Emissões registradas com sucesso!";
                    } else {
                        echo "Algo deu errado. Tente novamente.";
                    }
                    unset($stmt_insert);
                }
            }
        } else {
            echo "Algo deu errado. Tente novamente.";
        }
        unset($stmt);
    }
    unset($pdo);
}
?>

<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <title>Calculadoras de Emissão de Carbono</title>
    <link rel="stylesheet" href="calculos.css">
</head>
<body>
        <!-- partial:index.partial.html -->
<nav class="dropdownmenu">
  <ul>
    <li><a href="../Loginpage/index.html">Login</a></li>
    <li><a href="../Calculationpage/calculos.html">Calcule</a></li>
    <li><a href="../EcoHistoria/EcoHistoria.html">EcoHistória</a></li>
    <li><a href="../index.html">Home</a></li>
  </ul>
</nav>
<!-- partial -->

    <div class="container">
        <h1 id="text-question">O que podemos calcular para você hoje<a id="color-calcular">com a Green Education?</a></h1>
        <nav class="texts-and-buttons">
        

        <nav class="text ponteiro" id="glass-effect" onclick="openCalculation('transport')">
          <img class="icones" src="imagens/icones/viagensAereas.png">
          <p class="text-title"> Cálculo de Transporte</p>
          <p class="text-specific">
            Promova a mobilidade sustentável, se possível, dê preferência por caminhar, andar de bicicleta ou utilizar transporte público em vez de carro particular. Isso causa menos impacto!
          </p>
<p class="text-hidden">Transporte </p>
        </button>
        </nav>
        

         <nav class="text ponteiro" id="glass-effect" onclick="openCalculation('energy')">
          <img class="icones" src="imagens/icones/energia.png">
          <p class="text-title"> Cálculo de Energia</p>
          <p class="text-specific">
            Economize energia, aproveite a luz natural e mantenha cortinas e persianas abertas durante o dia; considere a instalação de painéis solares para gerar energia renovável e sustentável.
          </p>
        <p class="text-hidden">Energia </p>
        </button>
         </nav>
        

        <nav class="text ponteiro" id="glass-effect" onclick="openCalculation('airTravel')">
          <img class="icones" src="imagens/icones/viagensAereas.png">
          <p class="text-title"> Cálculo de Viagens Aéreas</p>
          <p class="text-specific">
            Escolha voos diretos, participe de programas de compensação de carbono e prefira linhas aéreas comprometidas com a sustentabilidade, para wue o planeta não sofra.
          </p>
        <p class="text-hidden">Viagens</p>
        </button>
        </nav>
        

        <nav class="text ponteiro" id="glass-effect" onclick="openCalculation('refrigerantGas')">
          <img class="icones" src="imagens/icones/residuos.png">
          <p class="text-title"> Cálculo de Gases Refrigerantes</p>
          <p class="text-specific">
            Utilize gases refrigerantes ecológicos: opte por sistemas de refrigeração que usem substâncias menos prejudiciais à camada de ozônio e que tenham menor potencial de aquecimento global.
          </p>
        <p class="text-hidden">Gases </p>
        </button>
        </nav>
        

        <nav class="text ponteiro" id="glass-effect" onclick="openCalculation('water')">
          <img class="icones" src="imagens/icones/agua.png">
          <p class="text-title"> Cálculo de Consumo de Água</p>
          <p class="text-specific">
            Instale dispositivos economizadores em torneiras e chuveiros, conserte vazamentos rapidamente e reutilize a água da chuva para regar plantas e limpar áreas externas.
          </p>
        <p class="text-hidden">Água </p>
        </button>
        </nav>
        
        <nav class="text ponteiro" id="glass-effect" onclick="openCalculation('residues')">
          <img class="icones" src="imagens/icones/residuos.png">
          <p class="text-title"> Cálculo de Resíduos</p>
          <p class="text-specific">
            Gerencie resíduos adequadamente: recicle e reutilize materiais sempre que possível, reduza o consumo de plásticos descartáveis, optando por alternativas sustentáveis.
          </p>


        <p class="text-hidden">Resíduos </p>
        </button>
        </nav>
        </nav>
    </div>

    <div id="overlay" class="overlay" onclick="closeCalculation()"></div>

    <div id="calculation-container" class="calculation-container">
        <span class="close" onclick="closeCalculation()">×</span>
        <!-- O conteúdo do cálculo será injetado aqui -->
    </div>
    <script type="text/javascript">
      function openCalculation(type) {
            document.getElementById('overlay').style.display = 'block';
            var container = document.getElementById('calculation-container');
            container.style.display = 'block';
            container.innerHTML = '<span class="close" onclick="closeCalculation()">×</span>'; // Reset content

            if (type === 'transport') {
                // Conteúdo para cálculo de emissão por transporte
                container.innerHTML += `
                <form action="calculos.php" method="post">
                <input type="hidden" name="segmento" value="transporte">
                    <h2>Emissão por Transporte</h2>
                    <p>Calculadora para estimar a emissão de carbono com base no meio de transporte utilizado e na distância percorrida.</p>
                    <label for="transporte">Escolha o meio de transporte:</label>
                    <select id="transporte">
                        <option value="automovel">Automóvel</option>
                        <option value="motocicleta">Motocicleta</option>
                        <option value="transporte_publico">Transporte Público</option>
                    </select><br><br>
                    <label for="distancia">Distância em quilômetros:</label>
                    <input type="text" id="distancia"><br><br>
                    <input type="checkbox" id="ida_volta">
                    <label for="ida_volta">É ida e volta?</label><br><br>
                    <button onclick="calcularEmissaoTransporte()" class="botao-escondido">Calcular</button>
                    <input type="submit" value="Registrar Emissão" class="botao-escondidodois">
                    <p id="resultadoTransporte" name="valor" required></p>
    </form>
                `;
            } else if (type === 'energy') {
                // Conteúdo para cálculo de emissão por energia
                container.innerHTML += `
                <form action="calculos.php" method="post">
                <input type="hidden" name="segmento" value="energia">
                    <h2>Emissão por Energia</h2>
                    <p>Calculadora para determinar a emissão de carbono com base no consumo de energia em KWh.</p>
                    <label for="energia">Insira o consumo de energia em KWh:</label>
                    <input type="text" id="energia"><br><br>
                    <button onclick="calcularEmissaoEnergia()" class="botao-escondido">Calcular</button>
                    <input type="submit" value="Registrar Emissão" class="botao-escondidodois">
                    <p id="resultadoEnergia" name="valor" required></p>
    </form>
                `;
            } else if (type === 'airTravel') {
                // Conteúdo para cálculo de emissão por viagens aéreas
                container.innerHTML += `
                <form action="calculos.php" method="post">
                <input type="hidden" name="segmento" value="viagensAereas">
                    <h2>Emissão por Viagens Aéreas</h2>
                    <p>Calculadora para estimar a emissão de carbono de viagens aéreas com base na distância percorrida.</p>
                    <label for="distanciaAerea">Insira a distância percorrida em Km:</label>
                    <input type="text" id="distanciaAerea"><br><br>
                    <button onclick="calcularEmissaoAerea()" class="botao-escondido">Calcular</button>
                    <input type="submit" value="Registrar Emissão" class="botao-escondidodois">
                    <p id="resultadoAerea" name="valor" required></p>
    </form>
                `;
            } else if (type === 'refrigerantGas') {
                // Conteúdo para cálculo de emissão por gases refrigerantes
                container.innerHTML += `
                <form action="calculos.php" method="post">
                <input type="hidden" name="segmento" value="gasesRefrigerantes">
                    <h2>Emissão por Gases Refrigerantes</h2>
                    <p>Calculadora para determinar a emissão de carbono com base na quantidade de gás refrigerante que escapou durante uma manutenção.</p>
                    <label for="gas">Escolha o tipo de gás:</label>
                    <select id="gas">
                        <option value="R-454A">R-454 A</option>
                        <option value="HFC-32">HFC -32 ou R32</option>
                        <option value="R-452">R- 452</option>
                        <option value="HFC-134a">HFC-134 a</option>
                        <option value="R-407C">R-407 C</option>
                        <option value="R-410A">R 410 A</option>
                        <option value="R-404A">R 404 A</option>
                    </select><br><br>
                    <label for="quantidadeGas">Insira a quantidade de gás em Kg:</label>
                    <input type="text" id="quantidadeGas"><br><br>
                    <button onclick="calcularEmissaoGas()" class="botao-escondido">Calcular</button>
                    <input type="submit" value="Registrar Emissão" class="botao-escondidodois">
                    <p id="resultadoGas" name="valor" required></p>
    </form>
                `;
            } else if (type === 'water') {
                // Conteúdo para cálculo de emissão por consumo de água
                container.innerHTML += `
                <form action="calculos.php" method="post">
                <input type="hidden" name="segmento" value="agua">
                    <h2>Emissão por Consumo de Água</h2>
                    <p>Calculadora para estimar a emissão de carbono com base no consumo de água.</p>
                    <label for="consumoAgua">Insira o consumo de água em m³:</label>
                    <input type="text" id="consumoAgua"><br><br>
                    <button onclick="calcularEmissaoAgua()" class="botao-escondido">Calcular</button>
                    <input type="submit" value="Registrar Emissão" class="botao-escondidodois">
                    <p id="resultadoAgua" name="valor" required></p>
    </form>
                `;
            } else if (type === 'residues') {
                // Conteúdo para cálculo de emissão por resíduos sólidos
                container.innerHTML += `
                <form action="calculos.php" method="post">
                <input type="hidden" name="segmento" value="residuos">
                    <h2>Emissão por Resíduos Sólidos</h2>
                    <p>Calculadora para calcular a emissão de carbono com base na quantidade de resíduos sólidos.</p>
                    <label for="residuosSolidos">Insira a quantidade de resíduos sólidos em Kg:</label>
                    <input type="text" id="residuosSolidos"><br><br>
                    <button onclick="calcularEmissaoResiduos()" class="botao-escondido">Calcular</button>
                    <input type="submit" value="Registrar Emissão" class="botao-escondidodois">
                    <p id="resultadoResiduos" name="valor" required></p>
    </form>
                `;
            }
        }

        function calcularEmissaoTransporte() {
            var transporte = document.getElementById('transporte').value;
            var distancia = parseFloat(document.getElementById('distancia').value);
            var idaVolta = document.getElementById('ida_volta').checked ? 2 : 1;
            var emissoes = {
                'automovel': 0.000148,
                'motocicleta': 0.0000448,
                'transporte_publico': 0.0000566
            };
            var emissao = emissoes[transporte] * distancia * idaVolta;
                        document.getElementById('resultadoTransporte').innerText = "Emissão de carbono: " + (emissao * 1000).toFixed(2) + " kg";
        }

        function calcularEmissaoEnergia() {
            var energia = parseFloat(document.getElementById('energia').value);
            var fatorEmissao = 42.6; // Este é um valor hipotético para o exemplo
            var emissao = energia * fatorEmissao;
            document.getElementById('resultadoEnergia').innerText = "Emissão de carbono: " + emissao.toFixed(2) + " kg";
        }

        function calcularEmissaoAerea() {
            var distancia = parseFloat(document.getElementById('distanciaAerea').value);
            var fatorEmissao = distancia <= 500 ? 0.00012988 : distancia <= 3700 ? 0.00008107 : 0.00010195;
            var emissao = distancia * fatorEmissao;
            document.getElementById('resultadoAerea').innerText = "Emissão de carbono: " + (emissao * 1000).toFixed(6) + " kg";
        }

        function calcularEmissaoGas() {
            var gas = document.getElementById('gas').value;
            var quantidade = parseFloat(document.getElementById('quantidadeGas').value);
            var fatoresEmissao = {
                'R-454A': 237,
                'HFC-32': 677,
                'R-452': 675,
                'HFC-134a': 1300,
                'R-407C': 1624,
                'R-410A': 1924,
                'R-404A': 3943
            };
            var emissao = (quantidade * fatoresEmissao[gas]) / 1000;
            document.getElementById('resultadoGas').innerText = "Emissão de carbono: " + emissao.toFixed(3) + " kg";
        }

        function calcularEmissaoAgua() {
            var consumoAgua = parseFloat(document.getElementById('consumoAgua').value);
            var fatorEmissao = 0.0176; // Este é um valor hipotético para o exemplo
            var emissao = consumoAgua * fatorEmissao;
            document.getElementById('resultadoAgua').innerText = "Emissão de carbono: " + emissao.toFixed(2) + " kg";
        }

        function calcularEmissaoResiduos() {
            var residuosSolidos = parseFloat(document.getElementById('residuosSolidos').value);
            var fatorEmissao = 0.061; // Este é um valor hipotético para o exemplo
            var emissao = residuosSolidos * fatorEmissao;
            document.getElementById('resultadoResiduos').innerText = "Emissão de carbono: " + emissao.toFixed(2) + " kg";
        }

        function closeCalculation() {
            document.getElementById('overlay').style.display = 'none';
            document.getElementById('calculation-container').style.display = 'none';
        }
    </script>
</body>
</html>