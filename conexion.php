    <?php
    // Obtener variables de entorno
    $dbHost = getenv('DB_HOST');
    $dbUser = getenv('DB_USER'); // Renombrado para evitar confusión con el usuario del login
    $dbPass = getenv('DB_PASSWORD'); // Renombrado
    $dbName = 'pruebaclase';

    if (!$dbHost || !$dbUser || $dbPass === false) {
        throw new \RuntimeException('Faltan variables de entorno para la conexión a la base de datos.');
    }
    // DSN con charset utf8mb4
    $dsn = "mysql:host={$dbHost};dbname={$dbName};charset=utf8mb4";
    
    try {
        $options = [
            // Excepciones en errores
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            // Fetch como array asociativo
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Desactivar emulación de prepares
            PDO::ATTR_EMULATE_PREPARES   => false,

            // Asegurar la conexión TLS hacia Azure Database for MySQL
            PDO::MYSQL_ATTR_SSL_CA        => '/etc/ssl/certs/BaltimoreCyberTrustRoot.crt.pem',
            // Desactivamos la validación del certificado SSL
            PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT => false,
        ];

        // Crear la conexión PDO
        $pdo = new PDO($dsn, $dbUser, $dbPass, $options);

        // Ejemplo: consulta sencilla
        $stmt = $pdo->query('SELECT NOW() AS fecha_actual;');
        $fila = $stmt->fetch();
        echo "Conectado correctamente. Hora del servidor: " . $fila['fecha_actual'];

        try {

                if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['login'])) {
                    
                    $login = trim($_POST['login']); 

                    if (empty($login)) {
                        echo "Por favor, introduce un nombre de usuario.";
                    }
                    else{          
                        $sql = "INSERT INTO prueba (contenido, fecha_acceso) VALUES (:contenido, NOW())";
                        $stmt = $pdo->prepare($sql);
                        $stmt->bindParam(':contenido', $login, PDO::PARAM_STR);
                        
                        if ($stmt->execute()) 
                            echo "Usuario " . htmlspecialchars($login) . " registrado exitosamente usando sentencia preparada. Fecha insertada con NOW()";            
                    }                            

            } catch (\PDOException $e) {
                    echo "Error al intentar guardar los datos en la base de datos. ";
            }        
    } catch (PDOException $e) {
        error_log('Error de conexión PDO: ' . $e->getMessage());
        echo "Error al conectar con la base de datos: " . htmlspecialchars($e->getMessage());
        exit;
    }
  
    
?>
