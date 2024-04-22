<?php
class Relac{
   private $DataNasc = "";
   private $Idade = 0;

   public function calculaIdade($DataNasc){
      list($dia, $mes, $ano) = explode('/', $DataNasc);  // Separa em dia, mês e ano
      //Descobre que dia é hoje e retorna a unix timestamp
      $hoje = mktime(0, 0, 0, date('m'), date('d'), date('Y'));
      // Descobre a unix timestamp da data de nascimento do fulano
      $nascimento = mktime( 0, 0, 0, $mes, $dia, $ano);
      //Cálculo da idade
      $Idade = floor((((($hoje - $nascimento) / 60) / 60) / 24) / 365.25); // .25 para os anos bisextos
      return $Idade;
   }
}