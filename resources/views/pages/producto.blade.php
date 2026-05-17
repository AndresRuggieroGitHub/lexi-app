@extends('layouts.site', ['title' => 'Lexi | Premium', 'description' => 'Planes premium de Lexi para ampliar límites de ejercicios y funciones avanzadas.', 'robots' => 'index,follow'])

@section('content')
<main id="mainContent" class="page-main container section-space">
  <h1 class="mb-4">Planes Premium</h1>
  <p class="text-muted mb-4">El plan gratuito sigue siendo útil. Premium amplía límites y desbloquea ejercicios avanzados.</p>

  <section class="plan-grid">
    <article class="product-info">
      <h2 class="h4">Premium mensual</h2>
      <p class="product-price">9 EUR / mes</p>
      <ul>
        <li>Más generaciones de ejercicios con IA</li>
        <li>Estadísticas de progreso ampliadas</li>
        <li>Recomendaciones personalizadas</li>
      </ul>
      <button class="btn btn-success mt-3" type="button" data-add-cart data-id="plan-premium-mensual" data-name="Plan Premium mensual" data-price="9">Añadir al carrito</button>
    </article>

    <article class="product-info">
      <h2 class="h4">Premium anual</h2>
      <p class="product-price">79 EUR / año</p>
      <ul>
        <li>Todo lo del plan mensual</li>
        <li>Ahorro frente al pago mes a mes</li>
        <li>Acceso prioritario a nuevas funciones</li>
      </ul>
      <button class="btn btn-success mt-3" type="button" data-add-cart data-id="plan-premium-anual" data-name="Plan Premium anual" data-price="79">Añadir al carrito</button>
    </article>
  </section>
</main>
@endsection