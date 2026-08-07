<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

function sharkdevelop_has_service_landing_content( int $post_id ): bool {
	return str_contains( (string) get_post_field( 'post_content', $post_id ), 'service-landing' );
}

function sharkdevelop_mobile_apps_default_content(): string {
	return <<<'HTML'
<!-- wp:group {"tagName":"section","className":"service-landing service-landing--mobile alignfull","layout":{"type":"constrained"}} -->
<section class="wp-block-group service-landing service-landing--mobile alignfull"><!-- wp:group {"tagName":"section","className":"service-mobile-hero alignfull","layout":{"type":"constrained"}} -->
<section class="wp-block-group service-mobile-hero alignfull"><!-- wp:group {"className":"container service-mobile-hero__inner","layout":{"type":"constrained"}} -->
<div class="wp-block-group container service-mobile-hero__inner"><!-- wp:group {"className":"service-mobile-hero__content","layout":{"type":"constrained"}} -->
<div class="wp-block-group service-mobile-hero__content"><!-- wp:paragraph {"className":"eyebrow"} -->
<p class="eyebrow">Mobile app development</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":1} -->
<h1 class="wp-block-heading">Mobile apps people want to use</h1>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>We design and build iOS and Android apps for customer services, internal operations, and new digital products, from a focused MVP to an evolving platform.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons {"className":"service-mobile-hero__actions"} -->
<div class="wp-block-buttons service-mobile-hero__actions"><!-- wp:button {"className":"button--cta"} -->
<div class="wp-block-button button--cta"><a class="wp-block-button__link wp-element-button" href="/contacts/">Let&rsquo;s talk</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group -->

<!-- wp:image {"id":9579,"sizeSlug":"large","linkDestination":"none","className":"service-mobile-hero__media"} -->
<figure class="wp-block-image size-large service-mobile-hero__media"><img src="/wp-content/uploads/2026/07/smsbus-background-1024x480.webp" alt="SMSBUS public transport mobile app" class="wp-image-9579"/></figure>
<!-- /wp:image --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"service-section service-section--scenarios alignfull","layout":{"type":"constrained"}} -->
<section class="wp-block-group service-section service-section--scenarios alignfull"><!-- wp:group {"className":"container","layout":{"type":"constrained"}} -->
<div class="wp-block-group container"><!-- wp:group {"className":"service-section__heading","layout":{"type":"constrained"}} -->
<div class="wp-block-group service-section__heading"><!-- wp:paragraph {"className":"eyebrow"} -->
<p class="eyebrow">When mobile is the right move</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">For products that<br>move with people</h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"service-scenarios","layout":{"type":"constrained"}} -->
<div class="wp-block-group service-scenarios"><!-- wp:group {"className":"service-scenario","layout":{"type":"constrained"}} -->
<div class="wp-block-group service-scenario"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Customer services in the pocket</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Give customers a faster way to browse, book, pay, manage an account, find what they need, and receive timely updates.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"service-scenario","layout":{"type":"constrained"}} -->
<div class="wp-block-group service-scenario"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Operations beyond the desk</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Equip field teams and staff with the workflows, data, notifications, and connected services they need away from a desktop.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"service-scenario","layout":{"type":"constrained"}} -->
<div class="wp-block-group service-scenario"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">An idea ready to test</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Turn a new product concept into a focused MVP that people can use, learn from, and help you evolve with confidence.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"service-section service-section--dark service-section--offerings alignfull","layout":{"type":"constrained"}} -->
<section class="wp-block-group service-section service-section--dark service-section--offerings alignfull"><!-- wp:group {"className":"container service-offerings__inner","layout":{"type":"constrained"}} -->
<div class="wp-block-group container service-offerings__inner"><!-- wp:group {"className":"service-section__heading","layout":{"type":"constrained"}} -->
<div class="wp-block-group service-section__heading"><!-- wp:paragraph {"className":"eyebrow"} -->
<p class="eyebrow">What we build</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">A mobile product is more than a set of screens</h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"service-offerings","layout":{"type":"constrained"}} -->
<div class="wp-block-group service-offerings"><!-- wp:group {"className":"service-offering","layout":{"type":"constrained"}} -->
<div class="wp-block-group service-offering"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Product definition</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>User journeys, requirements, and a focused scope for the first release.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"service-offering","layout":{"type":"constrained"}} -->
<div class="wp-block-group service-offering"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">UX and interface design</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Flows and interfaces designed for real tasks, device constraints, and everyday use.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"service-offering","layout":{"type":"constrained"}} -->
<div class="wp-block-group service-offering"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">iOS and Android development</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Native or cross-platform implementation, tested across the devices and platforms that matter.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"service-offering","layout":{"type":"constrained"}} -->
<div class="wp-block-group service-offering"><!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Backend and integrations</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>APIs, payments, maps, notifications, data systems, and third-party services working as one product.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:sharkdevelop/selected-projects {"projectIds":[9666,9629,9631,9643,9653,9657]} /-->

<!-- wp:group {"tagName":"section","className":"service-section service-section--process alignfull","layout":{"type":"constrained"}} -->
<section class="wp-block-group service-section service-section--process alignfull"><!-- wp:group {"className":"container","layout":{"type":"constrained"}} -->
<div class="wp-block-group container"><!-- wp:group {"className":"service-section__heading service-section__heading--process","layout":{"type":"constrained"}} -->
<div class="wp-block-group service-section__heading service-section__heading--process"><!-- wp:paragraph {"className":"eyebrow"} -->
<p class="eyebrow">From idea to release</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">A clear path to a product people can use</h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"service-process","layout":{"type":"constrained"}} -->
<div class="wp-block-group service-process"><!-- wp:group {"className":"service-phase","layout":{"type":"constrained"}} -->
<div class="wp-block-group service-phase"><!-- wp:paragraph {"className":"service-phase__number"} -->
<p class="service-phase__number">01</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Define the product</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Clarify users, priorities, constraints, and the first release that makes sense to build.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"service-phase","layout":{"type":"constrained"}} -->
<div class="wp-block-group service-phase"><!-- wp:paragraph {"className":"service-phase__number"} -->
<p class="service-phase__number">02</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Design the experience</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Shape flows and interfaces around the decisions people need to make on a small screen.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"service-phase","layout":{"type":"constrained"}} -->
<div class="wp-block-group service-phase"><!-- wp:paragraph {"className":"service-phase__number"} -->
<p class="service-phase__number">03</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Build and connect</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Develop the app, connect the services behind it, and test the journeys that matter most.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group -->

<!-- wp:group {"className":"service-phase","layout":{"type":"constrained"}} -->
<div class="wp-block-group service-phase"><!-- wp:paragraph {"className":"service-phase__number"} -->
<p class="service-phase__number">04</p>
<!-- /wp:paragraph -->

<!-- wp:heading {"level":3} -->
<h3 class="wp-block-heading">Launch and learn</h3>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Prepare the release, support the first users, and use what you learn to set the next priorities.</p>
<!-- /wp:paragraph --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"service-section service-section--faq alignfull","layout":{"type":"constrained"}} -->
<section class="wp-block-group service-section service-section--faq alignfull"><!-- wp:group {"className":"container service-faq","layout":{"type":"constrained"}} -->
<div class="wp-block-group container service-faq"><!-- wp:group {"className":"service-section__heading","layout":{"type":"constrained"}} -->
<div class="wp-block-group service-section__heading"><!-- wp:paragraph {"className":"eyebrow"} -->
<p class="eyebrow">Mobile app questions</p>
<!-- /wp:paragraph -->

<!-- wp:heading -->
<h2 class="wp-block-heading">What clients usually want to know</h2>
<!-- /wp:heading --></div>
<!-- /wp:group -->

<!-- wp:details -->
<details class="wp-block-details"><summary>Should we build a native or cross-platform app?</summary><!-- wp:paragraph -->
<p>It depends on the product, required device capabilities, existing technology, timeline, and how the app will evolve. We compare the trade-offs before choosing an approach.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details -->
<details class="wp-block-details"><summary>Can you develop an MVP?</summary><!-- wp:paragraph -->
<p>Yes. We help define the first release around the journeys that need to be tested, so the product can reach real users without carrying unnecessary scope.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details -->
<details class="wp-block-details"><summary>Can you work on an existing app?</summary><!-- wp:paragraph -->
<p>Yes. We begin by understanding the current product, codebase, user problems, and the next business goal before proposing the right scope of work.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details -->

<!-- wp:details -->
<details class="wp-block-details"><summary>Do you handle the backend and integrations too?</summary><!-- wp:paragraph -->
<p>Yes. Mobile apps often depend on backend systems, third-party services, payments, maps, and notifications. We can build or extend the parts required for the product to work as one system.</p>
<!-- /wp:paragraph --></details>
<!-- /wp:details --></div>
<!-- /wp:group --></section>
<!-- /wp:group -->

<!-- wp:group {"tagName":"section","className":"service-detail-cta alignfull","layout":{"type":"constrained"}} -->
<section class="wp-block-group service-detail-cta alignfull"><!-- wp:group {"className":"container service-detail-cta__inner","layout":{"type":"constrained"}} -->
<div class="wp-block-group container service-detail-cta__inner"><!-- wp:group {"className":"service-detail-cta__content","layout":{"type":"constrained"}} -->
<div class="wp-block-group service-detail-cta__content"><!-- wp:heading -->
<h2 class="wp-block-heading">Have a mobile product in mind?</h2>
<!-- /wp:heading -->

<!-- wp:paragraph -->
<p>Tell us what you are building, what is already in place, and where you need help. We will help you define the right next step.</p>
<!-- /wp:paragraph -->

<!-- wp:buttons -->
<div class="wp-block-buttons"><!-- wp:button {"className":"button--cta"} -->
<div class="wp-block-button button--cta"><a class="wp-block-button__link wp-element-button" href="/contacts/">Let&rsquo;s talk</a></div>
<!-- /wp:button --></div>
<!-- /wp:buttons --></div>
<!-- /wp:group --></div>
<!-- /wp:group --></section>
<!-- /wp:group --></section>
<!-- /wp:group -->
HTML;
}
