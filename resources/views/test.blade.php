<!DOCTYPE html>
<html>
<head>
    <title>Test</title>
</head>
<body>
    <h1>Test Blade</h1>
    <p>{{ 'Si ves esto, Blade funciona: ' . date('Y-m-d H:i:s') }}</p>
    @if(true)
        <p>Las directivas @if funcionan</p>
    @endif
</body>
</html>