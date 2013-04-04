Using Respect\Validation on Forms
=================================

Instruções gerais [no README.md principal](https://github.com/Respect/samples/blob/master/README.md)

Sobre
-----

Este exemplo mostra como usar PHP puro e apenas o Respect\Validation para
manipular a validação de um formulário, estado e mensagens de erro. Isto
está no arquivo index.php.

A maior parte do código é padronizado e é inteiramente comentado a cada linha
após a 80ª coluna de texto para que voce possa acompanhar a lógica.

O código é dividio em componentes MVC mas é representado em um único arquivo.
Eles poderiam ser separados em arquivos diferentes, mas foi mantido assim para
se manter conciso. Cada camada é marcada com algo similar a `/* Controller */`
para fins educativos.

O Respect\Validation é responsável por validar os dados do formulário submetido
e caso a validação falhe ele irá reportar as mensagens de erro específicas.
Isto é feito por estes dois snippets que podem ser encontrados no arquivo
index.php:

### Validating

```php
<?php
$validAccount = v::arr()                                                        // Vamos verificar ser é um array...
                 ->key('first', $n = v::string()->notEmpty()->length(3, 32))    // Com uma chave "first" contendo uma string de 3 a 32 caracteres;
                 ->key('last',  $n)                                             // Reusamos a mesma regra para a chave "last".
                 ->key('day', v::notEmpty())                                    // Deve possuir uma chave chamada "date" não vazia
                 ->key('month', v::notEmpty())                                  // Deve possuir uma chave chamada "month" não vazia
                 ->key('year', v::notEmpty())                                   // Deve possuir uma chave chamada "year" não vazia
                 ->call(function ($acc) {                                       // Chama esta função com o array passado (será $_POST)
                    return sprintf(                                             // Formata a string...
                        '%04d-%02d-%02d',                                       // Para este formata de data, preenchendo os números com zeros.
                        $acc['year'],
                        $acc['month'],
                        $acc['day']
                    );
                 }, v::date('Y-m-d')->minimumAge($minimumAge))                  // Então obtemos a string formatada e validamos a data e a idade mínima.
                 ->setName('the New Account');                                  // Damos um nome a esta regra!
```


### Obtendo os Erros

```php
<?php
    try {                                                                       // Inicia uma verificação para ser usada com o Respect\Validation
        $validAccount->assert($account);
        $account['messages'] = array("Success!");                               // Caso ela passe na validação, isto que iremos dizer!

    } catch (ValidationException $invalidAccount) {                             // Caso falhe...
        $account['messages'] = array_filter(
            array_values($invalidAccount->findMessages(                         // Pegará as mensagens para estas chaves
                array(
                    $validAccount->getName(),                                   // Mensagem para o nome que setamos anteriormente
                    'first.length',                                             // Encontra validador "length" para a chave "first"
                    'first.notEmpty' => 'Primeiro nome não pode em branco',     // Se quiser você pode sobrescrever a mensagem de erro
                    'last.length',
                    'last.notEmpty'  => 'Last name must not be empty',
                    'day.notEmpty'   => 'Birth day name must not be empty',
                    'year.notEmpty'  => 'Birth month name must not be empty',
                    'month.notEmpty' => 'Birth year name must not be empty',
                    'date',
                    'minimumAge'
                )
            ))
        );
    }
```

Sinta-se livre para vasculhar o código, fazer mudanças e abrir issues se quiser. Todo feedback será consirerado.
