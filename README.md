## Sobre a API

Crawler Project é uma aplicação que busca as informações sobre moedas de um site definido utilizando um **Crawler**.
Para o desenvolvimento desse projeto foi utilizando **PHP** com o Framework **Laravel** e para banco de dados foi 
utilizado **MySql**.

## Configurações Para Uso

### Criação das Tabelas No Banco de Dados

```bash
CREATE TABLE `coins_data_currency_locations` (
`id` int NOT NULL AUTO_INCREMENT,
`location` varchar(100) NOT NULL,
`icon` varchar(100) DEFAULT NULL,
PRIMARY KEY (`id`),
UNIQUE KEY `id_UNIQUE` (`id`)
)

CREATE TABLE `api_search_crawler`.`coins_data` (
`id` INT(10) NOT NULL AUTO_INCREMENT,
`codigo` VARCHAR(100) NULL,
`numero` INT(10) NULL,
`casas_decimais` INT(10) NULL,
`moeda` VARCHAR(100) NULL,
`idfk_currency_locations` INT(10) NULL,
PRIMARY KEY (`id`),
UNIQUE INDEX `id_UNIQUE` (`id` ASC) VISIBLE,
UNIQUE INDEX `codigo_UNIQUE` (`codigo` ASC) VISIBLE,
UNIQUE INDEX `numero_UNIQUE` (`numero` ASC) VISIBLE,
UNIQUE INDEX `casas_decimais_UNIQUE` (`casas_decimais` ASC) VISIBLE,
UNIQUE INDEX `moeda_UNIQUE` (`moeda` ASC) VISIBLE,
INDEX `fk_coins_data_1_idx` (`idfk_currency_locations` ASC) VISIBLE,
CONSTRAINT `fk_coins_data_1`
FOREIGN KEY (`idfk_currency_locations`)
REFERENCES `api_search_crawler`.`coins_data_currency_locations` (`id`)
ON DELETE NO ACTION
ON UPDATE NO ACTION);
```

### Iniciando Docker

```bash
$ docker-compose up
```

### Realizando a chamada para a aplicação

Utilizando o postman realizar uma chamada POST para a URL **http://localhost:8080/api/search** com o body tendo uma 
das seguintes informações:

```bash
{  
  "code": "GBP"
}
-------------------------------------------------------------------------------
{  
  "code_list": ["GBP", "GEL", "HKD"]  
}
-------------------------------------------------------------------------------
{  
  "number": [242]  
}
-------------------------------------------------------------------------------
{
  "number_lists": [242, 324]  
}
```
