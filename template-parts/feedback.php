<section class="feedback">
	<div class="pattern"></div>
	<div class="container">
		<div class="blur"></div>


		<div class="feedback__grid">
			<div class="feedback__content">
				<h2 class="h2">Заинтересовались продукцией?</h2>
				<div class="feedback__text">Получите продукцию по выгодной цене с быстрой доставкой. Оставьте заявку — и мы
					подберём для вас оптимальное решение!</div>
			</div>
			<div class="feedback__bottom">
				<div class="feedback__bottom-text">Или просто позвоните</div> <br>
				<a href="tel:<?php echo the_opt('phone_1'); ?>" class="feedback__bottom-phone"><?php echo the_opt('phone_1'); ?></a>
			</div>
			<form data-vgform data-vgform-name='Форма обратной связи' class="feedback__form form-feedback" >
				<h3 class="form-feedback__title h3">Оставьте заявку прямо сейчас</h3>
				<div class="form-feedback__field">
					<label for="name" class="form-feedback__label">Как к вам обращаться?</label>
					<input type="text" name="name" id="name" placeholder="ФИО" class="form-feedback__input" data-vgfrom-label='Имя'>
					<div class="form-error"></div>
				</div>
				<div class="form-feedback__field"><label for="phone" class="form-feedback__label">Номер телефона</label>
					<input type="tel" name="phone" id="phone" placeholder="+7(999) 999-99-99" class="form-feedback__input" data-vgfrom-label='Телефон'>
				</div>
				<div class="checkbox form-feedback__field form-feedback__field--checkbox">
					<label class="checkbox__label">
						<input name="agree" type="checkbox" class="checkbox__input" data-vgform-agree data-vgform-exception>
						<span class="checkbox__box"></span>
						<span class="checkbox__text form-feedback__politics">Я согласен (-на) с <a href="#">политикой
								конфиденциальности</a> и даю согласие на обработку персональных данных</span>
					</label>
				</div>
				<button type="submit" class="form-feedback__btn btn btn--fill">Заказать звонок</button>
			</form>
		</div>
	</div>

</section>