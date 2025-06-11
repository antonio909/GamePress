<?php
/**
 * Bootstrap file for setting the ABSPATH constant
 * and loading the config.php file. The config.php
 * file will then load the settings.php file, which
 * will then set up the GamePress environment.
 *
 * If the config.php file is not found then an error
 * will be displayed asking the visitor to set up the
 * config.php file.
 *
 * Will also search for config.php in GamePress parent
 * directory to allow the GamePress directory to remain
 * untouched.
 *
 * @package GamePress
 */

/** Define ABSPATH as this file's directory */
if ( ! defined( 'ABSPATH' ) ) {
        define( 'ABSPATH', __DIR__ . '/' );
}

/**
 * The error_reporting() function can be disabled in php.ini. On systems where that is the case,
 * it's best to add a dummy function to the config.php file, but as this call to the function
 * is run prior to config.php loading, it is wrapped in a function_exists() check.
 */
if ( function_exists( 'error_reporting' ) ) {
        /*
         * Initialize error reporting to a know set of levels.
         *
         * This will be adapted in debug_mode() located in includes/load.php based on DEBUG.
         * @see https://www.php.net/manual/en/errorfunc.constants.php List of known error levels.
         */
        // Isso configura o PHP para exibir ou registrar erros de núcleo, compilação, análise, e erros e avisos definidos pelo usuário, bem como erros recuperáveis.
        error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
}

// Tenta carregar o arquivo de configuração principal 'config.php';
// Este arquivo contém as configurações específicas da sua instalação GamePress (como detalhes do banco de dados).
if ( file_exists( ABSPATH . 'config.php' ) ) {

        // Se 'config.php' for encontrado no diretório raiz (ABSPATH), ele é incluído.
        require_once ABSPATH . 'config.php';

// Caso contrário, verifica uma alternativa: se 'config.php' existe no diretório raiz e 'gp-settings.php' (outro arquivo de configuração potencial) não existe.
} elseif ( @file_exists( dirname( ABSPATH ) . '/config.php' ) && ! @file_exists( dirname( ABSPATH ) . '/settings.php' ) ) {

        // Se a condição acima for verdadeira, inclui o 'config.php' do diretório raiz.
        // O '@' antes de 'file_exists' suprime quaisquer avisos caso o arquivo não possa ser acessado.
        require_once dirname( ABSPATH ) . '/config.php';

// Se nenhum dos arquivos 'gp-config.php' for encontrado nas localizações esperadas, isso indica que o GamePress não está configurado.        
} else {

        // Define a constante 'INC' como 'includes', que é o diretório para os arquivos internos do GamePress.
        define( 'INC', 'includes' );
        // Inclui arquivos essenciais para iniciar o GamePress, como a versão, compatibilidade e funções de carregamento.
        require_once ABSPATH . INC . '/version.php';
        require_once ABSPATH . INC . '/compat.php';
        require_once ABSPATH . INC . '/load.php';

        // Verifica as versões do PHP e MySQL para garantir que são compatíveis.
        check_php_mysql_versions();

        // Corrige variáveis do servidor, se necessário, para garantir que o ambiente está configurado corretamente.
        fix_server_vars();

        // Define o diretório de conteúdo do GamePress.
        define( 'CONTENT_DIR', ABSPATH . 'content' );
        // Inclui o arquivo de funções principais.
        require_once ABSPATH . INC . '/functions.php';

        // Constrói o caminho para o script de configuração inicial.
        $path = guess_url() . '/admin/setup-config.php';

        // Verifica se a URL da requisição atual não contém 'setup-config'.
        // Isso significa que o usuário não está na página de configuração, mas a configuração é necessária.
        if ( ! str_contains( $_SERVER['REQUEST_URI'], 'setup-config' ) ) {
                // Redireciona o navegador para a página de configuração.
                header( 'Location: ', $path );
                exit; // Termina a execução do script após o redirecionamento.
        }

        // Carrega as traduções antecipadamente, se aplicável.
        load_translation_early();

        // Exibe uma mensagem de erro e termina a execução do script.
        die( $die, __( 'GamePress &rsaquo; Error' ) );
}
