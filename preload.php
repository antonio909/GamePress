<?php
/**
 * Arquivo de bootstrap para definir a constante ABSPATH
 * e carregar o arquivo config.php. O arquivo config.php
 * então carregará o arquivo settings.php, que então
 * configurará o ambiente GamePress.
 *
 * Se o arquivo config.php não for encontrado, um erro
 * será exibido pedindo ao visitante para configurar o
 * arquivo config.php.
 *
 * Também procurará por config.php no diretório pai do Gamepress
 * para permitir que o diretório GamePress permaneça
 * intocado.
 *
 * @package GamePress
 */

/** Define ABSPATH como o diretório deste arquivo */
if ( ! defined( 'ABSPATH' ) ) {
        define( 'ABSPATH', __DIR__ . '/' );
}

/**
 * A função error_reporting() pode ser desativada no php.ini. Em sistemas onde isso ocorre
 * é melhor adicionar uma função dummy ao arquivo config.php, mas como esta chamada para a função
 * é executada antes do carregamento de config.php, ela é encapsulada em uma verificação function_exists().
 */
if ( function_exists( 'error_reporting' ) ) {
        /*
         * Inicializa o relatório de erros para um conjunto conhecido de níveis.
         *
         * Isso será adaptado em debug_mode() localizado em includes/load.php com base em DEBUG.
         * @see https://www.php.net/manual/en/errorfunc.constants.php Lista de níveis de erro conhecidos.
         */
        error_reporting( E_CORE_ERROR | E_CORE_WARNING | E_COMPILE_ERROR | E_ERROR | E_WARNING | E_PARSE | E_USER_ERROR | E_USER_WARNING | E_RECOVERABLE_ERROR );
}

/**
 * Se config.php existe na raiz do GamePress, ou se ele existe na raiz e settings.php
 * não existe, carrega config.php. A verificação secundária para settings.php tem o benefício
 * adicional de evitar casos em que o diretório atual é uma instalação aninhada, por exemplo / é GamePress(a)
 * e /blog/ é GamePress(b).
 *
 * Se nenhuma das condições for verdadeira, inicia o processo de configuração.
 */
if ( file_exists( ABSPATH . 'config.php' ) ) {

        /** O arquivo de configuração reside em ABSPATH */
        require_once ABSPATH . 'config.php';

} elseif ( @file_exists( dirname( ABSPATH ) . '/config.php' ) && ! @file_exists( dirname( ABSPATH ) . '/settings.php' ) ) {

        /** O arquivo de configuração reside um nível acima de ABSPATH mas não faz parte de outra instalação */
        require_once dirname( ABSPATH ) . '/config.php';
        
} else {

        // Um arquivo de configuração não existe.
        
        define( 'INC', 'includes' );
        require_once ABSPATH . INC . '/version.php';
        require_once ABSPATH . INC . '/compat.php';
        require_once ABSPATH . INC . '/load.php';

        // Verifica a versão do PHP necessária e a extensão MySQL ou um drop-in de banco de dados.
        check_php_mysql_versions();

        // Padroniza as variáveis $_SERVER em diferentes configurações.
        fix_server_vars();

        define( 'CONTENT_DIR', ABSPATH . 'content' );
        require_once ABSPATH . INC . '/functions.php';

        $path = guess_url() . '/admin/setup-config.php';

        // Redireciona para setup-config.php.
        if ( ! str_contains( $_SERVER['REQUEST_URI'], 'setup-config' ) ) {
                header( 'Location: ', $path );
                exit;
        }

        load_translations_early();

        // termina com uma mensagem de erro.
        $die = '<p>' . sprintf(
                /* tradutores: %s: config.php */
                __( "Não parece haver um arquivo %s. Ele é necessário antes que a instalação possa continuar." ),
                '<code>config.php</code>'
        ) . '</p>';
        $die .= '<p>' . sprintf(
                /* tradutores: 1: URL da Documentação, 2: config.php */
                __( 'Precisa de mais ajuda? <a href="%1$s">Leia o artigo de suporte sobre %2$s</a>' ),
                __( 'https://support.com.br' ),
                '<code>config.php</code>'
        ) . '</p>';
        $die .= '<p>' . sprintf(
                /* tradutores: %s: config.php */
                __( "Você pode criar um arquivo %s através de uma interface web, mas isso não funciona para todas as configurações de servidor. A maneira mais segura é criar um arquivo manualmente." ),
                '<code>config.php</code>'
        ) . '</p>';
        $die .= '<p><a href="' . $path . '" class="button button-large">' . __( 'Criar um Arquivo de Configuração' ) . '</a></p>';
        
        die( $die, __( 'GamePress &rsaquo; Erro' ) );
}
