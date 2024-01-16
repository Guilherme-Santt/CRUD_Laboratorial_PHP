<?php
    if(!isset($_SESSION)){
        session_start();
        if(!isset($_SESSION['usuario'])){
            die('Você não está logado!' . '<a href="login.php">Clique aqui para logar</a>');
        }    
}
    include('conexao.php');
    $id = $_SESSION['usuario'];
    $sqlcode = "SELECT * FROM clientes WHERE id = '$id'";
    $query = $mysqli->query($sqlcode);
    $cliente = $query->fetch_assoc();

    function formatar_data($data){
        return implode('/', array_reverse(explode('-', $data)));
    };
    // FORMATAR TELEFONE PARA VISUALIZAÇÃO COM CARACTERES
    function formatar_telefone($telefone){
        $ddd = substr ($telefone, 0, 2);
        $parte1 = substr ($telefone, 2, 5);
        $parte2 = substr ($telefone, 7);
        return "($ddd) $parte1-$parte2";
    }
    include('conexao.php');
    // COMANDO SQL PARA CONSULTAR CLIENTES
    $sql_clientes   = "SELECT * FROM clientes";
    // COMANDO QUERY, PARA EXECUTAR COMANDO SQL
    $query_clientes = $mysqli->query($sql_clientes) or die($mysqli->error);
    // COMANDO NUM ROWS, PARA CONTAR QUANTIDADE DADOS NO BANCO
    $num_clientes   = $query_clientes->num_rows;

?>

<?php
    function limpar_texto($str){ 
        return preg_replace("/[^0-9]/", "", $str); 
    }

    $error = "";
    if(count($_POST) > 0){
        $email = $_POST['email'];
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $nome = $_POST['nome'];
        $telefone = $_POST['telefone'];
        $nascimento = $_POST['nascimento'];

        if(empty($_POST['email']) || !filter_var($email, FILTER_VALIDATE_EMAIL)){
        $error = "preencha o campo email!";
        }
        if(empty($_POST['senha'])){
            $error = "preencha o campo senha!";
        }  
        if(empty($_POST['nome']) || Strlen($nome) < 3 || Strlen($nome) > 100){
            $error = "preencha o campo nome!";
        }
        if(!empty($nascimento)){
            $pedacos = explode('/', $nascimento);
            if(count($pedacos) == 3){
                $nascimento = implode ('-', array_reverse($pedacos)); 
            }
            else{
                $erro = "A data de nascimento deve ser preenchido no padrão dia/mes/ano";
            }
        }    
        if(!empty($telefone)){
            $telefone = limpar_texto($telefone);
            if(strlen($telefone) != 11){
                $erro = "O telefone deve ser preenchido no padrão (11) 98888-8888";
            }
        }
        if($error){

        }else{
            $sql_codeverify = "SELECT * FROM clientes WHERE email = '$email'";
            $query_c = $mysqli->query($sql_codeverify);
            $usuario = $query_c->fetch_assoc();
            $verify = $query_c->num_rows;
                if($verify){
                    $error = "usuário já cadastrado!";
                }
                else{
                    $sqlinsert = "INSERT INTO clientes (nome, email, telefone, nascimento, data, senha)  values ('$nome', '$email', '$telefone', '$nascimento', NOW(), '$senha')";
                    $queryinsert = $mysqli->query($sqlinsert);
                        if($queryinsert){
                            $sucess = "Cadastrado com sucesso";
                        }
                }
        }

    }   
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<link rel="stylesheet" href="index.css">
<link rel="stylesheet" href="usuarios.css">
<link rel="stylesheet" href="normalize.css">
<body> 
        <!-- Header  *NAV* - Mensagem central superior -->
        <header class="h-g">
        <form class="h-f">
            <p class="white">Olá, <b><?php echo $cliente['nome']?></b></p>
        </form>
        <!-- *HEADER* Menu & Logo central  -->
        <img onclick="lmenu()" class="h-img" src="imagens/hamburger.png">
        <div id="lh" class="h-menu"><br>
            <a onclick="fmenu()">X</a>
            <Ul>
                <a href="index.php"><li>Páginal ínicial</li></a>
                <a href="usuarios.php"><li>Usuários</li></a>
                <a href="pacientes.php"><li>Listagem de pacientes</li></a>
                <a href="cadastro_pacientes.php"><li>Cadastro de pacientes</li></a>
                <a href="cadastro_exames.php"><li>Cadastro de exames</li></a>
                <a href="logout.php"><li>Encerrar sessão</li></a>
            </Ul>
            <div class="icons">
                <img src="imagens/instagram.png">
                <img src="imagens/facebook.png">
                <img src="imagens/tiktok.png">
                <img src="imagens/youtube.png">
                <img src="imagens/whatsapp.png">
            </div>
        </div>
        <p class="white">Unidade: <b><?php echo $cliente['unidade']?></b></p>
    </header> 
    <div class="full">
        <div class="From_Cadastrados">
            <h1>Usuários cadastrados</h1>
            <p>Esses são os usuários cadastrados no seu sistema</p>
            <table border="1" cellpadding="10">
                <thead>
                    <th>ID</th>
                    <th>Nome</th>
                    <th>E-mail</th>
                    <th>Telefone</th>
                    <th>Data de cadastro</th>
                    <th>Ações</th>
                </thead>
                <tbody> 
                <?php 
                    if($num_clientes == 0) { 
                ?> 
                <tr>
                    <td colspan="7">Nenhum usuário foi encontrado!</td>
                </tr>
                <?php }
                    else{ 
                        while($cliente = $query_clientes->fetch_assoc()){
                            $telefone ="Não informado!";
                            if(!empty($cliente['telefone'])){
                                $telefone = formatar_telefone($cliente['telefone']);   
                            }
                                $nascimento = "Nascimento não informada!";
                            if(!empty($cliente['nascimento'])){
                                $nascimento = formatar_data($cliente['nascimento']);
                                }
                                $data_cadastro = date("d/m/y H:i:s", strtotime($cliente['data']));
                    ?>     
                    <tr>
                        <td><?php echo $cliente['id']?>     </td>
                        <td><?php echo $cliente['nome']?>   </td>
                        <td><?php echo $cliente['email']?>  </td>
                        <td><?php echo $telefone; ?>  </td>
                        <td><?php echo $nascimento ?>   </td>
                        <td><?php echo $data_cadastro;?>    </td>
                        <td>
                        <a class="edit" href="editar_usuario.php?id=<?php echo $cliente['id']?>">Editar</a>
                        <a class="error" href="deletar_usuario.php?id=<?php echo $cliente['id']?>">Deletar</a>
                        </td>
                    </tr>             
                <?php
                    }
                    }
                ?>
                </tbody>
            </table>
        </div><br>
        <button onclick="lcadastro()">Cadastrar clientes</button><br>
        <div class="insert_cadastrar" id="cadastrar_usuarios">
            <a onclick="fcadastro()">X</a>
            <h1>Cadastrar usuários</h1>
            <form action="" method="POST">
                <label>Email</label>
                <input class="input_edit" type="email" value="<?php if(isset($_POST['email'])) echo $_POST['email']; ?>" name="email"><br><br>

                <label>Nome</label>
                <input class="input_edit" type="text" value="<?php if(isset($_POST['nome'])) echo $_POST['nome']; ?>" name="nome"><br><br>

                <label>Nascimento</label>
                <input class="input_edit" type="text" value="<?php if(isset($_POST['nascimento'])) echo $_POST['nascimento']; ?>" name="nascimento" placeholder="dia/mês/ano"><br><br>
                
                <label>Telefone:</label>
                <input class="input_edit" value ="<?php if(isset($_POST['telefone'])) echo $_POST['telefone']; ?>" placeholder="(11) 98888-8888" type="text" name="telefone"><br><br>
                
                <label>Senha</label>
                <input class="input_edit" type="password" value="<?php if(isset($_POST['senha'])) echo $_POST['senha']; ?>" name="senha"><br><br>

                <button type="submit" name="cadastrar">Cadastrar</button>
            </form>
            <?php 
                if(isset($sucess)){echo'<p class="sucess">'. $sucess . '</p>' ;}
                if($error){echo '<p class="error">'. $error . '</p>' ;}   
            ?>
        </div>
    </div>    
    <script src="usuarios.js"></script>
    <script src="index.js"></script>
</body>
</html>
