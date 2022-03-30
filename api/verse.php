<?php

use GraphQL\Client;
use GraphQL\Exception\QueryError;
use GraphQL\QueryBuilder\QueryBuilder;
use GraphQL\RawObject;

$client = new Client(
    'https://lds-scripture-api.herokuapp.com/v1/graphql'
);

$builder = (new QueryBuilder())
    ->setVariable('verseId', 'Int', true)
    ->selectField(
        (new QueryBuilder('verses'))
            ->setArgument('where', new RawObject('{ id: { _eq: $verseId } }'))
            ->selectField('verseNumber')
            ->selectField('scriptureText')
    );
$gql = $builder->getQuery();

try {
    $variablesArray = ['verseId' => $_GET['id']];
    $results = $client->runQuery($gql, true, $variablesArray);
} catch (QueryError $exception) {
    // Catch query error and desplay error details
    print_r($exception->getErrorDetails());
    exit;
}

echo json_encode($results->getData()['verses']);
