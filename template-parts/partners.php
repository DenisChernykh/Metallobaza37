<section class="partners">
	<div class="container">
		<div class="partners__top top">
			<h2 class="h2"> Партнеры</h2>
			<p>Наши партнёры — это гарантия надёжности и стабильных поставок для ваших строительных и промышленных проектов.</p>
		</div>
		<?php
		$partnerNames = [
			'Северсталь',
			'НЛМК',
			'НТПЗ',
			'КТЗ',
			'ОМК'
		];
		?>

		<div class="partners__grid">
			<?php foreach ($partnerNames as $i => $name) : ?>
				<?php get_template_part('template-parts/partners-card', null, [
					'name' => $name,
					'index' => $i + 1
				]); ?>
			<?php endforeach; ?>
		</div>
	</div>
</section>