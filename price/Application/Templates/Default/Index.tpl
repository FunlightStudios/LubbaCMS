{assign var=header value=$this->createView('Header.tpl', $pageId)}
{$this->displayView($header)}
{assign var=language value=$this->getLanguage()->json()}
			<main class="mdl-layout__content">
				<div class="mdl-layout__tab-panel is-active" id="overview">
					<div class="mdl-grid">
						{foreach item=furniture from=$furnitures->getAll()}
						<div class="inline-block mdl-cell">
							<div class="mdl-tooltip mdl-tooltip--large mdl-tooltip--top" for="furniture-{$furniture->getRow()->id}">{$furniture->getRow()->name|htmlspecialchars}</div>
							<section class="section--center mdl-grid mdl-grid--no-spacing mdl-shadow--2dp">
								<div class="mdl-card mdl-cell mdl-cell--12-col">
									<div class="mdl-card__supporting-text">
										<a class="furniture-link" href="{$settings->getSitePath()}/furniture/{$furniture->getRow()->id}">
											<div id="furniture-{$furniture->getRow()->id}" class="furni-preview" style="background: url('{$settings->getFurnituresPath()}/{$furniture->getRow()->image|htmlspecialchars}') 50% no-repeat;"></div>
										</a>
									</div>
									<div class="price-info">
										<img src="{$settings->getWebPath()}/images/credits.png" />
										<span>{$furniture->getRow()->price} {$language['Credits']}</span>
									</div>
								</div>
								<button class="mdl-menu-button mdl-button mdl-js-button mdl-js-ripple-effect mdl-button--icon" id="btn{$furniture->getRow()->id}">
									<i class="material-icons">more_vert</i>
								</button>
								<ul class="mdl-menu mdl-js-menu mdl-menu--bottom-right" for="btn{$furniture->getRow()->id}">
									<li><a href="{$settings->getSitePath()}/furniture/{$furniture->getRow()->id}" class="mdl-menu__item">{$language['Cardview']['View']}</a></li>
								</ul>
							</section>
						</div>
						{/foreach}
					</div>
				</div>
			</main>
{assign var=footer value=$this->createView('Footer.tpl', $pageId)}
{$this->displayView($footer)}
