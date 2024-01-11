{assign var=header value=$this->createView('Header.tpl', $pageId)}
{$this->displayView($header)}
{assign var=language value=$this->getLanguage()->json()}
			<main class="mdl-layout__content">
				<div class="mdl-layout__tab-panel is-active margin-from-header" id="overview">
					<section class="section--center mdl-grid mdl-grid--no-spacing mdl-shadow--2dp">
						<div class="mdl-card mdl-cell mdl-cell--12-col">
							<div class="mdl-card__supporting-text">
								<h4>{$language['Titles']['Login']}</h4>

								<div class="formMsg">{if isset($text)}{$text->filter()}{/if}</div>
								<div id="p2" class="formLoader mdl-progress mdl-js-progress mdl-progress__indeterminate"></div>

								<form id="login" method="post">
									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label w-fill">
										<input class="mdl-textfield__input" type="text" id="tb_username" name="username" />
										<label class="mdl-textfield__label" for="tb_username">{$language['Login']['Username']}</label>
									</div>

									<div class="mdl-textfield mdl-js-textfield mdl-textfield--floating-label w-fill">
										<input class="mdl-textfield__input" type="password" id="tb_password" name="password" />
										<label class="mdl-textfield__label" for="tb_password">{$language['Login']['Password']}</label>
									</div>

									<input type="submit" class="mt-15 w-fill mdl-button mdl-js-button mdl-button--raised mdl-js-ripple-effect" data-upgraded=",MaterialButton,MaterialRipple" value="{$language['Login']['Submit']}" />
								</form>
							</div>
						</div>
					</section>
				</div>
			</main>
{assign var=footer value=$this->createView('Footer.tpl', $pageId)}
{$this->displayView($footer)}
