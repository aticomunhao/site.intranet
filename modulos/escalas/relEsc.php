<?php
session_start(); 
if(!isset($_SESSION["usuarioID"])){
    session_destroy();
    header("Location: ../../index.php");
}

require_once(dirname(dirname(__FILE__))."/config/abrealas.php");
date_default_timezone_set('America/Sao_Paulo'); 


//01-0001 Dia - minuto
?>
    <style>
        .quadro1min {
                font-size: 90%;
                min-width: 10px;
                min-height: 10px;
                border: 1px solid;
                border-radius: 5px;
                text-align: center;
                cursor: pointer;
        }
    </style>
    <script>
        $(document).ready(function(){
            document.getElementById("01").style.backgroundColor = "#0090FF";

        });
    </script>
<table style="margin: 0 auto;">
    <tr> <!-- dias -->
        <td id="01" class="quadro1min">01</td>
        <td id="02" class="quadro1min">02</td>
        <td><div id="03" class="quadro1min">03</div></td>
        <td><div id="04" class="quadro1min">04</div></td>
        <td><div id="05" class="quadro1min">05</div></td>
        <td><div id="06" class="quadro1min">06</div></td>
        <td><div id="07" class="quadro1min">07</div></td>
        <td><div id="08" class="quadro1min">08</div></td>
        <td><div id="09" class="quadro1min">09</div></td>
        <td><div id="10" class="quadro1min">10</div></td>
        <td><div id="11" class="quadro1min">11</div></td>
        <td><div id="12" class="quadro1min">12</div></td>
        <td><div id="13" class="quadro1min">13</div></td>
        <td><div id="14" class="quadro1min">14</div></td>
        <td><div id="15" class="quadro1min">15</div></td>
        <td><div id="16" class="quadro1min">16</div></td>
        <td><div id="17" class="quadro1min">17</div></td>
        <td><div id="18" class="quadro1min">18</div></td>
        <td><div id="10" class="quadro1min">10</div></td>
        <td><div id="20" class="quadro1min">20</div></td>
        <td><div id="21" class="quadro1min">21</div></td>
        <td><div id="22" class="quadro1min">22</div></td>
        <td><div id="23" class="quadro1min">23</div></td>
        <td><div id="24" class="quadro1min">24</div></td>
        <td><div id="25" class="quadro1min">25</div></td>
        <td><div id="26" class="quadro1min">26</div></td>
        <td><div id="27" class="quadro1min">27</div></td>
        <td><div id="28" class="quadro1min">28</div></td>
        <td><div id="29" class="quadro1min">29</div></td>
        <td><div id="30" class="quadro1min">30</div></td>
        <td><div id="31" class="quadro1min">31</div></td>
    </tr>
    <tr> <!-- dia - minutos -->
        <td id="01-0010" class="quadro1min">01-0010</td>
        <td id="02-0010" class="quadro1min"><div></div></td>
        <td id="03-0010" class="quadro1min"></td>
        <td id="04-0010" class="quadro1min"></td>
        <td id="05-0010" class="quadro1min"></td>
        <td id="06-0010" class="quadro1min"></td>
        <td id="07-0010" class="quadro1min"></td>
        <td id="08-0010" class="quadro1min"></td>
        <td id="09-0010" class="quadro1min"></td>
        <td id="10-0010" class="quadro1min"></td>
        <td id="11-0010" class="quadro1min"></td>
        <td id="12-0010" class="quadro1min"></td>
        <td id="13-0010" class="quadro1min"></td>
        <td id="14-0010" class="quadro1min"></td>
        <td id="15-0010" class="quadro1min"></td>
        <td id="16-0010" class="quadro1min"></td>
        <td id="17-0010" class="quadro1min"></td>
        <td id="18-0010" class="quadro1min"></td>
        <td id="19-0010" class="quadro1min"></td>
        <td id="20-0010" class="quadro1min"></td>
        <td id="21-0010" class="quadro1min"></td>
        <td id="22-0010" class="quadro1min"></td>
        <td id="23-0010" class="quadro1min"></td>
        <td id="24-0010" class="quadro1min"></td>
        <td id="25-0010" class="quadro1min"></td>
        <td id="26-0010" class="quadro1min"></td>
        <td id="27-0010" class="quadro1min"></td>
        <td id="28-0010" class="quadro1min"></td>
        <td id="29-0010" class="quadro1min"></td>
        <td id="30-0010" class="quadro1min"></td>
        <td id="31-0010" class="quadro1min"></td>
    </tr>
    <tr> <!-- dia - minutos -->
        <td id="01-0020" class="quadro1min">01-0020</td>
        <td id="02-0020" class="quadro1min"></td>
        <td id="03-0020" class="quadro1min"></td>
        <td id="04-0020" class="quadro1min"></td>
        <td id="05-0020" class="quadro1min"></td>
        <td id="06-0020" class="quadro1min"></td>
        <td id="07-0020" class="quadro1min"></td>
        <td id="08-0020" class="quadro1min"></td>
        <td id="09-0020" class="quadro1min"></td>
        <td id="10-0020" class="quadro1min"></td>
        <td id="11-0020" class="quadro1min"></td>
        <td id="12-0020" class="quadro1min"></td>
        <td id="13-0020" class="quadro1min"></td>
        <td id="14-0020" class="quadro1min"></td>
        <td id="15-0020" class="quadro1min"></td>
        <td id="16-0020" class="quadro1min"></td>
        <td id="17-0020" class="quadro1min"></td>
        <td id="18-0020" class="quadro1min"></td>
        <td id="19-0020" class="quadro1min"></td>
        <td id="20-0020" class="quadro1min"></td>
        <td id="21-0020" class="quadro1min"></td>
        <td id="22-0020" class="quadro1min"></td>
        <td id="23-0020" class="quadro1min"></td>
        <td id="24-0020" class="quadro1min"></td>
        <td id="25-0020" class="quadro1min"></td>
        <td id="26-0020" class="quadro1min"></td>
        <td id="27-0020" class="quadro1min"></td>
        <td id="28-0020" class="quadro1min"></td>
        <td id="29-0020" class="quadro1min"></td>
        <td id="30-0020" class="quadro1min"></td>
        <td id="31-0020" class="quadro1min"></td>
    </tr>
    <tr> <!-- dia - minutos -->
        <td id="01-0030" class="quadro1min">01-0030</td>
        <td id="02-0030" class="quadro1min"></td>
        <td id="03-0030" class="quadro1min"></td>
        <td id="04-0030" class="quadro1min"></td>
        <td id="05-0030" class="quadro1min"></td>
        <td id="06-0030" class="quadro1min"></td>
        <td id="07-0030" class="quadro1min"></td>
        <td id="08-0030" class="quadro1min"></td>
        <td id="09-0030" class="quadro1min"></td>
        <td id="10-0030" class="quadro1min"></td>
        <td id="11-0030" class="quadro1min"></td>
        <td id="12-0030" class="quadro1min"></td>
        <td id="13-0030" class="quadro1min"></td>
        <td id="14-0030" class="quadro1min"></td>
        <td id="15-0030" class="quadro1min"></td>
        <td id="16-0030" class="quadro1min"></td>
        <td id="17-0030" class="quadro1min"></td>
        <td id="18-0030" class="quadro1min"></td>
        <td id="19-0030" class="quadro1min"></td>
        <td id="20-0030" class="quadro1min"></td>
        <td id="21-0030" class="quadro1min"></td>
        <td id="22-0030" class="quadro1min"></td>
        <td id="23-0030" class="quadro1min"></td>
        <td id="24-0030" class="quadro1min"></td>
        <td id="25-0030" class="quadro1min"></td>
        <td id="26-0030" class="quadro1min"></td>
        <td id="27-0030" class="quadro1min"></td>
        <td id="28-0030" class="quadro1min"></td>
        <td id="29-0030" class="quadro1min"></td>
        <td id="30-0030" class="quadro1min"></td>
        <td id="31-0030" class="quadro1min"></td>
    </tr>
    <tr> <!-- dia - minutos -->
        <td id="01-0040" class="quadro1min">01-0040</td>
        <td id="02-0040" class="quadro1min"></td>
        <td id="03-0040" class="quadro1min"></td>
        <td id="04-0040" class="quadro1min"></td>
        <td id="05-0040" class="quadro1min"></td>
        <td id="06-0040" class="quadro1min"></td>
        <td id="07-0040" class="quadro1min"></td>
        <td id="08-0040" class="quadro1min"></td>
        <td id="09-0040" class="quadro1min"></td>
        <td id="10-0040" class="quadro1min"></td>
        <td id="11-0040" class="quadro1min"></td>
        <td id="12-0040" class="quadro1min"></td>
        <td id="13-0040" class="quadro1min"></td>
        <td id="14-0040" class="quadro1min"></td>
        <td id="15-0040" class="quadro1min"></td>
        <td id="16-0040" class="quadro1min"></td>
        <td id="17-0040" class="quadro1min"></td>
        <td id="18-0040" class="quadro1min"></td>
        <td id="19-0040" class="quadro1min"></td>
        <td id="20-0040" class="quadro1min"></td>
        <td id="21-0040" class="quadro1min"></td>
        <td id="22-0040" class="quadro1min"></td>
        <td id="23-0040" class="quadro1min"></td>
        <td id="24-0040" class="quadro1min"></td>
        <td id="25-0040" class="quadro1min"></td>
        <td id="26-0040" class="quadro1min"></td>
        <td id="27-0040" class="quadro1min"></td>
        <td id="28-0040" class="quadro1min"></td>
        <td id="29-0040" class="quadro1min"></td>
        <td id="30-0040" class="quadro1min"></td>
        <td id="31-0040" class="quadro1min"></td>
    </tr>
    <tr> <!-- dia - minutos -->
        <td id="01-0050" class="quadro1min">01-0050</td>
        <td id="02-0050" class="quadro1min"></td>
        <td id="03-0050" class="quadro1min"></td>
        <td id="04-0050" class="quadro1min"></td>
        <td id="05-0050" class="quadro1min"></td>
        <td id="06-0050" class="quadro1min"></td>
        <td id="07-0050" class="quadro1min"></td>
        <td id="08-0050" class="quadro1min"></td>
        <td id="09-0050" class="quadro1min"></td>
        <td id="10-0050" class="quadro1min"></td>
        <td id="11-0050" class="quadro1min"></td>
        <td id="12-0050" class="quadro1min"></td>
        <td id="13-0050" class="quadro1min"></td>
        <td id="14-0050" class="quadro1min"></td>
        <td id="15-0050" class="quadro1min"></td>
        <td id="16-0050" class="quadro1min"></td>
        <td id="17-0050" class="quadro1min"></td>
        <td id="18-0050" class="quadro1min"></td>
        <td id="19-0050" class="quadro1min"></td>
        <td id="20-0050" class="quadro1min"></td>
        <td id="21-0050" class="quadro1min"></td>
        <td id="22-0050" class="quadro1min"></td>
        <td id="23-0050" class="quadro1min"></td>
        <td id="24-0050" class="quadro1min"></td>
        <td id="25-0050" class="quadro1min"></td>
        <td id="26-0050" class="quadro1min"></td>
        <td id="27-0050" class="quadro1min"></td>
        <td id="28-0050" class="quadro1min"></td>
        <td id="29-0050" class="quadro1min"></td>
        <td id="30-0050" class="quadro1min"></td>
        <td id="31-0050" class="quadro1min"></td>
    </tr>
    <tr> <!-- dia - minutos -->
        <td id="01-0100" class="quadro1min">01-0100</td>
        <td id="02-0100" class="quadro1min"></td>
        <td id="03-0100" class="quadro1min"></td>
        <td id="04-0100" class="quadro1min"></td>
        <td id="05-0100" class="quadro1min"></td>
        <td id="06-0100" class="quadro1min"></td>
        <td id="07-0100" class="quadro1min"></td>
        <td id="08-0100" class="quadro1min"></td>
        <td id="09-0100" class="quadro1min"></td>
        <td id="10-0100" class="quadro1min"></td>
        <td id="11-0100" class="quadro1min"></td>
        <td id="12-0100" class="quadro1min"></td>
        <td id="13-0100" class="quadro1min"></td>
        <td id="14-0100" class="quadro1min"></td>
        <td id="15-0100" class="quadro1min"></td>
        <td id="16-0100" class="quadro1min"></td>
        <td id="17-0100" class="quadro1min"></td>
        <td id="18-0100" class="quadro1min"></td>
        <td id="19-0100" class="quadro1min"></td>
        <td id="20-0100" class="quadro1min"></td>
        <td id="21-0100" class="quadro1min"></td>
        <td id="22-0100" class="quadro1min"></td>
        <td id="23-0100" class="quadro1min"></td>
        <td id="24-0100" class="quadro1min"></td>
        <td id="25-0100" class="quadro1min"></td>
        <td id="26-0100" class="quadro1min"></td>
        <td id="27-0100" class="quadro1min"></td>
        <td id="28-0100" class="quadro1min"></td>
        <td id="29-0100" class="quadro1min"></td>
        <td id="30-0100" class="quadro1min"></td>
        <td id="31-0100" class="quadro1min"></td>
    </tr>
    <tr> <!-- dia - minutos -->
        <td id="01-0110" class="quadro1min">01-0110</td>
        <td id="02-0110" class="quadro1min"></td>
        <td id="03-0110" class="quadro1min"></td>
        <td id="04-0110" class="quadro1min"></td>
        <td id="05-0110" class="quadro1min"></td>
        <td id="06-0110" class="quadro1min"></td>
        <td id="07-0110" class="quadro1min"></td>
        <td id="08-0110" class="quadro1min"></td>
        <td id="09-0110" class="quadro1min"></td>
        <td id="10-0110" class="quadro1min"></td>
        <td id="11-0110" class="quadro1min"></td>
        <td id="12-0110" class="quadro1min"></td>
        <td id="13-0110" class="quadro1min"></td>
        <td id="14-0110" class="quadro1min"></td>
        <td id="15-0110" class="quadro1min"></td>
        <td id="16-0110" class="quadro1min"></td>
        <td id="17-0110" class="quadro1min"></td>
        <td id="18-0110" class="quadro1min"></td>
        <td id="19-0110" class="quadro1min"></td>
        <td id="20-0110" class="quadro1min"></td>
        <td id="21-0110" class="quadro1min"></td>
        <td id="22-0110" class="quadro1min"></td>
        <td id="23-0110" class="quadro1min"></td>
        <td id="24-0110" class="quadro1min"></td>
        <td id="25-0110" class="quadro1min"></td>
        <td id="26-0110" class="quadro1min"></td>
        <td id="27-0110" class="quadro1min"></td>
        <td id="28-0110" class="quadro1min"></td>
        <td id="29-0110" class="quadro1min"></td>
        <td id="30-0110" class="quadro1min"></td>
        <td id="31-0110" class="quadro1min"></td>
    </tr>
    <tr> <!-- dia - minutos -->
        <td id="01-0120" class="quadro1min">01-0120</td>
        <td id="02-0120" class="quadro1min"></td>
        <td id="03-0120" class="quadro1min"></td>
        <td id="04-0120" class="quadro1min"></td>
        <td id="05-0120" class="quadro1min"></td>
        <td id="06-0120" class="quadro1min"></td>
        <td id="07-0120" class="quadro1min"></td>
        <td id="08-0120" class="quadro1min"></td>
        <td id="09-0120" class="quadro1min"></td>
        <td id="10-0120" class="quadro1min"></td>
        <td id="11-0120" class="quadro1min"></td>
        <td id="12-0120" class="quadro1min"></td>
        <td id="13-0120" class="quadro1min"></td>
        <td id="14-0120" class="quadro1min"></td>
        <td id="15-0120" class="quadro1min"></td>
        <td id="16-0120" class="quadro1min"></td>
        <td id="17-0120" class="quadro1min"></td>
        <td id="18-0120" class="quadro1min"></td>
        <td id="19-0120" class="quadro1min"></td>
        <td id="20-0120" class="quadro1min"></td>
        <td id="21-0120" class="quadro1min"></td>
        <td id="22-0120" class="quadro1min"></td>
        <td id="23-0120" class="quadro1min"></td>
        <td id="24-0120" class="quadro1min"></td>
        <td id="25-0120" class="quadro1min"></td>
        <td id="26-0120" class="quadro1min"></td>
        <td id="27-0120" class="quadro1min"></td>
        <td id="28-0120" class="quadro1min"></td>
        <td id="29-0120" class="quadro1min"></td>
        <td id="30-0120" class="quadro1min"></td>
        <td id="31-0120" class="quadro1min"></td>
    </tr>
    <tr> <!-- dia - minutos -->
        <td id="01-0130" class="quadro1min">01-0130</td>
        <td id="02-0130" class="quadro1min"></td>
        <td id="03-0130" class="quadro1min"></td>
        <td id="04-0130" class="quadro1min"></td>
        <td id="05-0130" class="quadro1min"></td>
        <td id="06-0130" class="quadro1min"></td>
        <td id="07-0130" class="quadro1min"></td>
        <td id="08-0130" class="quadro1min"></td>
        <td id="09-0130" class="quadro1min"></td>
        <td id="10-0130" class="quadro1min"></td>
        <td id="11-0130" class="quadro1min"></td>
        <td id="12-0130" class="quadro1min"></td>
        <td id="13-0130" class="quadro1min"></td>
        <td id="14-0130" class="quadro1min"></td>
        <td id="15-0130" class="quadro1min"></td>
        <td id="16-0130" class="quadro1min"></td>
        <td id="17-0130" class="quadro1min"></td>
        <td id="18-0130" class="quadro1min"></td>
        <td id="19-0130" class="quadro1min"></td>
        <td id="20-0130" class="quadro1min"></td>
        <td id="21-0130" class="quadro1min"></td>
        <td id="22-0130" class="quadro1min"></td>
        <td id="23-0130" class="quadro1min"></td>
        <td id="24-0130" class="quadro1min"></td>
        <td id="25-0130" class="quadro1min"></td>
        <td id="26-0130" class="quadro1min"></td>
        <td id="27-0130" class="quadro1min"></td>
        <td id="28-0130" class="quadro1min"></td>
        <td id="29-0130" class="quadro1min"></td>
        <td id="30-0130" class="quadro1min"></td>
        <td id="31-0130" class="quadro1min"></td>
    </tr>
    <tr> <!-- dia - minutos -->
        <td id="01-0140" class="quadro1min">01-0140</td>
        <td id="02-0140" class="quadro1min"></td>
        <td id="03-0140" class="quadro1min"></td>
        <td id="04-0140" class="quadro1min"></td>
        <td id="05-0140" class="quadro1min"></td>
        <td id="06-0140" class="quadro1min"></td>
        <td id="07-0140" class="quadro1min"></td>
        <td id="08-0140" class="quadro1min"></td>
        <td id="09-0140" class="quadro1min"></td>
        <td id="10-0140" class="quadro1min"></td>
        <td id="11-0140" class="quadro1min"></td>
        <td id="12-0140" class="quadro1min"></td>
        <td id="13-0140" class="quadro1min"></td>
        <td id="14-0140" class="quadro1min"></td>
        <td id="15-0140" class="quadro1min"></td>
        <td id="16-0140" class="quadro1min"></td>
        <td id="17-0140" class="quadro1min"></td>
        <td id="18-0140" class="quadro1min"></td>
        <td id="19-0140" class="quadro1min"></td>
        <td id="20-0140" class="quadro1min"></td>
        <td id="21-0140" class="quadro1min"></td>
        <td id="22-0140" class="quadro1min"></td>
        <td id="23-0140" class="quadro1min"></td>
        <td id="24-0140" class="quadro1min"></td>
        <td id="25-0140" class="quadro1min"></td>
        <td id="26-0140" class="quadro1min"></td>
        <td id="27-0140" class="quadro1min"></td>
        <td id="28-0140" class="quadro1min"></td>
        <td id="29-0140" class="quadro1min"></td>
        <td id="30-0140" class="quadro1min"></td>
        <td id="31-0140" class="quadro1min"></td>
    </tr>
    <tr> <!-- dia - minutos -->
        <td id="01-0150" class="quadro1min">01-0150</td>
        <td id="02-0150" class="quadro1min"></td>
        <td id="03-0150" class="quadro1min"></td>
        <td id="04-0150" class="quadro1min"></td>
        <td id="05-0150" class="quadro1min"></td>
        <td id="06-0150" class="quadro1min"></td>
        <td id="07-0150" class="quadro1min"></td>
        <td id="08-0150" class="quadro1min"></td>
        <td id="09-0150" class="quadro1min"></td>
        <td id="10-0150" class="quadro1min"></td>
        <td id="11-0150" class="quadro1min"></td>
        <td id="12-0150" class="quadro1min"></td>
        <td id="13-0150" class="quadro1min"></td>
        <td id="14-0150" class="quadro1min"></td>
        <td id="15-0150" class="quadro1min"></td>
        <td id="16-0150" class="quadro1min"></td>
        <td id="17-0150" class="quadro1min"></td>
        <td id="18-0150" class="quadro1min"></td>
        <td id="19-0150" class="quadro1min"></td>
        <td id="20-0150" class="quadro1min"></td>
        <td id="21-0150" class="quadro1min"></td>
        <td id="22-0150" class="quadro1min"></td>
        <td id="23-0150" class="quadro1min"></td>
        <td id="24-0150" class="quadro1min"></td>
        <td id="25-0150" class="quadro1min"></td>
        <td id="26-0150" class="quadro1min"></td>
        <td id="27-0150" class="quadro1min"></td>
        <td id="28-0150" class="quadro1min"></td>
        <td id="29-0150" class="quadro1min"></td>
        <td id="30-0150" class="quadro1min"></td>
        <td id="31-0150" class="quadro1min"></td>
    </tr>
    <tr> <!-- dia - minutos -->
        <td id="01-0200" class="quadro1min">01-0200</td>
        <td id="02-0200" class="quadro1min"></td>
        <td id="03-0200" class="quadro1min"></td>
        <td id="04-0200" class="quadro1min"></td>
        <td id="05-0200" class="quadro1min"></td>
        <td id="06-0200" class="quadro1min"></td>
        <td id="07-0200" class="quadro1min"></td>
        <td id="08-0200" class="quadro1min"></td>
        <td id="09-0200" class="quadro1min"></td>
        <td id="10-0200" class="quadro1min"></td>
        <td id="11-0200" class="quadro1min"></td>
        <td id="12-0200" class="quadro1min"></td>
        <td id="13-0200" class="quadro1min"></td>
        <td id="14-0200" class="quadro1min"></td>
        <td id="15-0200" class="quadro1min"></td>
        <td id="16-0200" class="quadro1min"></td>
        <td id="17-0200" class="quadro1min"></td>
        <td id="18-0200" class="quadro1min"></td>
        <td id="19-0200" class="quadro1min"></td>
        <td id="20-0200" class="quadro1min"></td>
        <td id="21-0200" class="quadro1min"></td>
        <td id="22-0200" class="quadro1min"></td>
        <td id="23-0200" class="quadro1min"></td>
        <td id="24-0200" class="quadro1min"></td>
        <td id="25-0200" class="quadro1min"></td>
        <td id="26-0200" class="quadro1min"></td>
        <td id="27-0200" class="quadro1min"></td>
        <td id="28-0200" class="quadro1min"></td>
        <td id="29-0200" class="quadro1min"></td>
        <td id="30-0200" class="quadro1min"></td>
        <td id="31-0200" class="quadro1min"></td>
    </tr>

<?php

        echo "<tr>";
            echo "<td id='01-0210' class='quadro1min' style='background-color: red;'>01-0210</td>";
            echo "<td id='02-0210' class='quadro1min'>02</td>";
            echo "<td id='03-0210' class='quadro1min'>03</td>";
            echo "<td id='04-0210' class='quadro1min'>04</td>";

        echo "</tr>";

?>
</table>