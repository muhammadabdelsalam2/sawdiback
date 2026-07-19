 <!-- <section class="section-padding">
    <div class="container">
        <div class="bg-dark p-5 rounded-5 text-center text-white position-relative overflow-hidden"
            style="background-color: #1a1a1a !important;">

            <h2 class="display-5 text-white mb-4">{{ __('app.cta_title') }}</h2>

            <p class="lead opacity-75 mb-5">{{ __('app.cta_text') }}</p>

            <button class="btn btn-primary-brand btn-lg px-5">{{ __('app.cta_button') }}</button>

            <div class="position-absolute bottom-0 start-0 w-100 h-10 bg-green opacity-10"
                style="background: var(--brand-green); height: 5px;"></div>
        </div>
    </div>
</section> -->

<section id="about" class="py-5 bg-cream about-section">

    <div class="container">

        <div class="about-wrapper">

            <div class="row align-items-center g-5">

                {{-- Image --}}
                <div class="col-lg-6">

                    <div class="about-image-wrapper">

                        <img src="{{ asset('assets/images/logo3.jpeg') }}"
                             class="about-image"
                             alt="EL-Sawady">

                    </div>

                </div>

                {{-- Content --}}
                <div class="col-lg-6">

                    <div class="about-content">

                        <h2 class="display-4 fw-bold mb-4">
                            {{ __('app.about_title') }}
                        </h2>

                        <p class="about-text mb-5">
                            {{ __('app.about_text') }}
                        </p>

                        <div class="about-stats">

                            <div class="about-stat-card">
                                <h3>15+</h3>
                                <span>{{ __('app.years_experience') }}</span>
                            </div>

                            <div class="about-stat-card">
                                <h3>100%</h3>
                                <span>{{ __('app.natural_products') }}</span>
                            </div>

                        </div>

                    </div>

                </div>

            </div>

        </div>

    </div>

</section>

<style>

    .about-section{
        background: #f6f4ef;
    }

    .about-wrapper{
        background: #fff;
        border-radius: 40px;
        padding: 60px;
        box-shadow: 0 20px 60px rgba(0,0,0,0.06);
        overflow: hidden;
    }

    .about-image-wrapper{
        width: 100%;
        height: 650px;
        border-radius: 30px;
        overflow: hidden;
        background: #f8f8f8;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .about-image{
        width: 100%;
        height: 100%;
        object-fit: contain;
        padding: 20px;
    }

    .about-content{
        padding-inline: 20px;
    }

    .about-text{
        font-size: 1.2rem;
        line-height: 2;
        color: #666;
    }

    .about-stats{
        display: flex;
        gap: 25px;
        margin-top: 40px;
        flex-wrap: wrap;
    }

    .about-stat-card{
        min-width: 170px;
        padding: 25px;
        border-radius: 24px;
        background: #f8faf7;
        border: 1px solid #e7eee4;
        text-align: center;
    }

    .about-stat-card h3{
        font-size: 2.5rem;
        font-weight: 800;
        color: #198754;
        margin-bottom: 10px;
    }

    .about-stat-card span{
        color: #666;
        font-size: 1rem;
    }

    @media(max-width: 992px){

        .about-wrapper{
            padding: 30px;
        }

        .about-image-wrapper{
            height: 400px;
        }

        .about-content{
            text-align: center;
            padding-inline: 0;
        }

        .about-stats{
            justify-content: center;
        }

    }

    @media(max-width: 576px){

        .about-wrapper{
            border-radius: 25px;
            padding: 20px;
        }

        .about-image-wrapper{
            height: 280px;
            border-radius: 20px;
        }

        .about-text{
            font-size: 1rem;
        }

        .about-stat-card{
            width: 100%;
        }

    }

</style>





