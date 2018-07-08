<?php
$values = [5, 10, 15, 20, 25, 30, 40, 50, 75, 100];
$options = [];
foreach($values as $value) $options[$value] = $value;
?>

<form action="admin-post.php" method="post">
    {{-- ADD NONCE AND ACTION --}}
    @include('partials.form-nonce-and-action')

    @include('form-items.select', [
        'name' => $countOptionName,
        'options' => $options,
    ])
</form>