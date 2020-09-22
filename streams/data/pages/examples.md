---
title: Examples
---
## Example

What would you like to know?

<br>

<h2>View Includes</h2>

<br>

<h2>"Local" Image Type</h2>

{!! Images::make('resources/img/deftones.jpg')->thumbnail('Tst') !!}
{!! Images::make('resources/img/deftones.jpg')->width(700) !!}
{!! Images::make('resources/img/deftones.jpg')->width(300)->grayscale() !!}

<h2>"Storage" Image Type</h2>

{!! Images::make('img/smashing-pumpkins.jpg') !!}
{!! Images::make('img/smashing-pumpkins.jpg')->width(700) !!}
{!! Images::make('img/smashing-pumpkins.jpg')->width(300)->grayscale() !!}
