<footer class="basement">
    <div class="container">
        <ul class="nav" role="tablist">
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#sitemap" role="tab">Карта сайта</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#feedback" role="tab">Обратная связь</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#contacts" role="tab">Контакты</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#delivery" role="tab">Доставка и оплата</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" data-toggle="tab" href="#return" role="tab">Обмен и возврат</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" data-toggle="tab" href="#news" role="tab">Новости</a>
                <div class="nav-arrow"></div>
            </li>
        </ul>
    </div>
    <div class="tab-wrapper">
        <div class="container">
            <div class="tab-content">
                <div class="tab-pane fade" id="sitemap" role="tabpanel">1</div>
                <div class="tab-pane fade" id="feedback" role="tabpanel">2</div>
                <div class="tab-pane fade" id="contacts" role="tabpanel">3</div>
                <div class="tab-pane fade" id="delivery" role="tabpanel">4</div>
                <div class="tab-pane fade" id="return" role="tabpanel">5</div>
                <div class="tab-pane fade show active" id="news" role="tabpanel">6</div>
            </div>
        </div>
        @php(dynamic_sidebar('sidebar-footer'))
    </div>
</footer>
