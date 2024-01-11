{assign var=header value=$this->createView('Header.tpl', $pageId)}
{$this->displayView($header)}
{assign var=language value=$this->getLanguage()->json()}
			<main class="mdl-layout__content">
				<div class="mdl-layout__tab-panel is-active margin-from-header" id="overview">
					<div class="mdl-cell mdl-cell--12-col">
						<section class="section--center mdl-grid mdl-grid--no-spacing mdl-shadow--2dp">
							<div class="mdl-card mdl-cell mdl-cell--12-col mdl-card__supporting-text">
								<h4>{$furniture->getRow()->name|htmlspecialchars}</h4>
								<div class="mdl-card__supporting-text text-center">
									<img src="{$settings->getFurnituresPath()}/{$furniture->getRow()->image|htmlspecialchars}" />
									<p style="padding-top: 10px">{$furniture->getRow()->description|htmlspecialchars}</p>
								</div>
							</div>
							<div class="price-info">
								<img src="{$settings->getWebPath()}/images/credits.png" />
								<span>{$furniture->getRow()->price} {$language['Credits']}</span>
							</div>
						</section>
					</div>
				</div>
			</main>
{assign var=footer value=$this->createView('Footer.tpl', $pageId)}
{$this->displayView($footer)}
