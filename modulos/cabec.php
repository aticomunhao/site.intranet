<!DOCTYPE html>
<html lang="pt-br">
    <title></title>
    <head>
        <style>
            .cabec1Esq{
                width: 10%;
            }
            .cabec1Cen{
                width: 47%; text-align: center;
            }
            .cabec1Dir{
                width: 40%; border: 1px solid blue; border-radius: 20px; background-color: #6C7AB3; background-image: url('imagens/ComunBannerLongo2.jpg'); background-repeat: no-repeat; background-position-x: center; background-position-y: center;
            }
			@media (max-width: 742px){
                .fontTrebuchet_Spacing{
                    font-size: 1rem; 
                    height: 12px;
                }
                .dataCabec{
                    font-size: .7rem; 
                }
                .cabec1Dir{
                    width: 40%; border: 1px solid blue; border-radius: 20px; background-color: #6C7AB3; background-image: url('imagens/bannerCurto.jpg'); background-repeat: no-repeat; background-position-x: center; background-position-y: center;
                }
/*                .cabec1Esq, .cabec1Cen, .cabec1Dir{
                    width: 100%;
                }
*/
			}
        </style>
    </head>
    <body>
        <div id="containerCabec" style="position: absolute; top: 0px; left: 0px; width: 100%; height: 60px;">
            <div id="container1Esq" class="cabec1Esq">
                <img src="imagens/Logo1.png" height="40px;">
            </div>
            <div id="container1Cen" class="fontSiteFamily cabec1Cen">
                <?php
                date_default_timezone_set('America/Sao_Paulo');
                $diasemana = array('Domingo', 'Segunda-feira', 'Terça-feira', 'Quarta-feira', 'Quinta-feira', 'Sexta-feira', 'Sábado');
                $data = date('Y-m-d');
                $diasemana_numero = date('w', strtotime($data));     
                ?>
                <div class="fontTrebuchet_Spacing" style="width: 99%; position: absolute; top: 2px; ">Comunhão Espírita de Brasília</div>
                <div class="dataCabec" style="width: 99%; position: absolute;"><?php echo $diasemana[$diasemana_numero].", ".date('d/m/Y'); ?></div>
            </div>
            <div id="container1Dir" class="cabec1Dir"></div>
        </div>
    </body>
</html>