<?php
/**
 * Suporte ao protocolo XML-RPC para GamePress
 *
 * @package GamePress
 */

/**
 * Define se essa página é uma requisição XML-RPC.
 *
 * @var bool
 */
define( 'XMLRPC_REQUEST', true ); // Define uma constante para indicar que é uma requisição XML-RPC.

// Descarta cookies desnecessários enviados por alguns clientes incorporados ao navegador.
$_COOKIE = array(); // Limpa o array de cookies para evitar problemas com clientes XML-RPC.

// $HTTP_RAW_POST_DATA foi depreciado no PHP 5.6 e removido no PHP 7.0.
// phpcs:disable PHPCompatibility.Variables.RemovedPredefinedGlobalVariables.http_raw_post_dataDeprecatedRemoved
if ( ! isset( $HTTP_RAW_POST_DATA ) ) {
        // Se $HTTP_RAW_POST_DATA não estiver definido, lê o conteúdo bruto da requisição.
        $HTTP_RAW_POST_DATA = file_get_contents( 'php://input' );
}

// Correção para mozBlog e outros casos onde '<?xml' não está na primeira linha.
$HTTP_RAW_POST_DATA = trim( $HTTP_RAW_POST_DATA ); // Remove espaços em branco do início e fim do conteúdo.
// phpcs:enable

/** Inclui o bootstrap para configurar o ambiente GamePress */
require_once __DIR__ . '/load.php'; // Carrega o arquivo de inicialização do GamePress.

if ( isset( $_GET['rsd'] ) ) { // Verifica se o parâmetro 'rsd' está presente na URL. (https://cyber.harvard.edu/blogs/gems/tech/rsd.html)
        // Define o cabeçalho Content-Type como XML e o charset do blog.
        header( 'Content-Type: text/xml; charset=' . get_option( 'blog_charset' ), true );
        // Imprime a declaração XML.
        echo '<?xml version="1.0" encoding="' . get_option( 'blog_charset' ) . '"?' . '>';
        ?>
<rsd version="1.0" xmlns="http://archipelago.phrasewise.com/rsd">
        <service>
                <engineName>GamePress</engineName>
                <engineLink>https://gamepress.com/</engineLink>
                <homePageLink><?php bloginfo_rss( 'url' ); ?></homePageLink>
                <apis>
                        <api name="GamePress" blogID="1" preferred="true" apiLink="<?php echo site_url( 'xmlrpc.php', 'rpc' ); ?>" />
                        <api name="Movable Type" blogID="1" preferred="false" apiLink="<?php echo site_url( 'xmlrpc.php', 'rpc' ); ?>" />
                        <api name="MetaWeblog" blogID="1" preferred="false" apiLink="<?php echo site_url( 'xmlrpc.php', 'rpc' ); ?>" />
                        <api name="Blogger" blogID="1" preferred="false" apiLink="<?php echo site_url( 'xmlrpc.php', 'rpc' ); ?>" />
                        <?php
                        /**
                         * Dispara quando APIs são adicionadas ao endpoint Really Simple Discovery (RSD).
                         *
                         * @link: https://cyber.harvard.edu/blogs/gems/tech/rsd.html
                         *
                         * @since 1.0.0
                         */
                        do_action( 'xmlrpc_rsd_apis' ); // Permite que outros plugins adicionem suas próprias APIs RSD.
                        ?>
                </apis>
        </service>
</rsd>
        <?php
        exit; // Termina a execução do script após exibir o RSD.
}

require_once ABSPATH . 'admin/includes/admin.php'; // Inclui arquivos administrativos.
require_once ABSPATH . INC . '/class-IXR.php'; // Inclui a classe IXR (para parsing XML-RPC).
require_once ABSPATH . INC . '/class-xmlrpc-server.php'; // Inclui a classe do servidor XML-RPC.

/**
 * Posts enviados via interface XML-RPC recebem este título.
 *
 * @name post_default_title
 * @var string
 */
$post_default_title = ''; // Define um título padrão para posts XML-RPC (inicialmente vazio).

/**
 * Filtra a classe usada para lidar com requisições XML-RPC.
 *
 * @since 1.0.0
 *
 * @param string $class O nome da classe do servidor XML-RPC.
 */
// Permite que outros plugins alterem a classe do servidor XML-RPC.
$xmlrpc_server_class = apply_filters( 'xmlrpc_server_class', 'xmlrpc_server' );
$xmlrpc_server       = new $xmlrpc_server_class(); // Instância do servidor XML-RPC.

// Dispara a requisição.
$xmlrpc_server->serve_request(); // Processa a requisição XML-RPC.

exit; // Termina a execução do script.

/**
 * logIO() - Escreve informações de log em um arquivo.
 *
 * @since 1.0.0
 * @deprecated 3.4.0 Use error_log()
 * @see error_log()
 *
 * @global int|bool $xmlrpc_logging para habilitar o log XML-RPC.
 *
 * @param string $io  Se é entrada ou saída.
 * @param string $msg Informação descrevendo o motivo do log.
 */
function logIO( $io, $msg ) {
        // Marca a função como depreciada, sugerindo o uso de error_log().
        _deprecated_function( __FUNCTION__, '3.4.0', 'error_log()' );
        // Se o log XML-RPC estiver habilitado, registra a mensagem.
        if ( ! empty( $GLOBALS['xmlrpc_logging'] ) ) {
                error_log( $io . ' - ' . $msg );
        }
}
