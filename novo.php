<?php


interface BancoDeDados {
    public function salvar(float $valor);
}

interface Notificador {
    public function enviar(string $mensagem);
}

interface CalculadoraDesconto {
    public function calcular(float $valor): float;
}


class MySQLDatabase implements BancoDeDados {
    public function salvar(float $valor) {
        echo "Salvando no MySQL: R$ {$valor} <br>";
    }
}

class MailNotifier implements Notificador {
    public function enviar(string $mensagem) {
        echo "E-mail enviado: {$mensagem} <br>";
    }
}


class DescontoVIP implements CalculadoraDesconto {
    public function calcular(float $valor): float {
        return $valor * 0.2;
    }
}

class DescontoRegular implements CalculadoraDesconto {
    public function calcular(float $valor): float {
        return $valor * 0.1;
    }
}


class Pedido
{
    private $valor;
    private $banco;
    private $notificador;
    private $estrategiaDesconto;

    public function __construct(
        float $valor, 
        CalculadoraDesconto $desconto, 
        BancoDeDados $db, 
        Notificador $notificador
    ) {
        $this->valor = $valor;
        $this->estrategiaDesconto = $desconto;
        $this->banco = $db;
        $this->notificador = $notificador;
    }

    public function processar()
    {
        $valorDesconto = $this->estrategiaDesconto->calcular($this->valor);
        $valorFinal = $this->valor - $valorDesconto;

        $this->banco->salvar($valorFinal);
        $this->notificador->enviar("Pedido processado: R$ {$valorFinal}");
    }
}



$banco = new MySQLDatabase();
$email = new MailNotifier();
$descontoVip = new DescontoVIP();

$pedido = new Pedido(100.00, $descontoVip, $banco, $email);
$pedido->processar();