<x-layouts.app>
    <x-slot name="title">
        {{ __('Zenvoyc - Professional Invoicing & Accounting for Modern Businesses') }}
    </x-slot>

    {{-- Hero Section --}}
    <x-section.hero class="w-full mb-8 md:mb-72">
        <div class="px-4 mx-auto text-center h-160 md:h-180">
            <x-pill class="text-primary-500 bg-primary-50">{{ __('Streamline Your Finances') }}</x-pill>
            <x-heading.h1 class="mt-4 font-bold text-primary-50">
                {{ __('Smart Invoicing for') }}
                <br class="hidden sm:block">
                {{ __('Smarter Businesses') }}
            </x-heading.h1>

            <p class="m-3 text-primary-50 text-lg">
                {{ __('Take full control of your business finances with Zenvoyc. Create professional invoices, track expenses, and get paid faster—all in one place.') }}
            </p>

            <div class="flex flex-col flex-wrap justify-center gap-4 mt-6 md:flex-row">
                <x-effect.glow></x-effect.glow>
                <x-button-link.secondary href="#pricing" class="self-center !py-3 px-8" elementType="a">
                    {{ __('Start Your Free Trial') }}
                </x-button-link.secondary>
                <x-button-link.primary-outline href="#features"
                                               class="bg-transparent self-center !py-3 text-white border-white">
                    {{ __('Explore Features') }}
                </x-button-link.primary-outline>
            </div>

            <x-user-ratings link="#testimonials" class="relative z-40 items-center justify-center mt-6">
                <x-slot name="avatars">
                    {{-- يمكنك الإبقاء على الصور الحالية أو استبدالها بصور عملاء حقيقيين لاحقاً --}}
                    <x-user-ratings.avatar
                        src="https://images.unsplash.com/photo-1560250097-0b93528c311a?auto=format&fit=crop&w=64&q=80"
                        alt="Business Owner 1"/>
                    <x-user-ratings.avatar
                        src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?auto=format&fit=crop&w=64&q=80"
                        alt="Freelancer 1"/>
                    <x-user-ratings.avatar
                        src="https://images.unsplash.com/photo-1519085360753-af0119f7cbe7?auto=format&fit=crop&w=64&q=80"
                        alt="CEO 1"/>
                </x-slot>
                {{ __('Trusted by 500+ business owners managing their growth with Zenvoyc.') }}
            </x-user-ratings>

            <div class="mx-auto md:max-w-3xl lg:max-w-5xl">
                {{-- صورة تعبيرية للوحة التحكم (Dashboard) --}}
                <img class="mt-8 transition drop-shadow-2xl hover:scale-101 rounded-2xl  "
                     src="{{URL::asset('images/features/admin-panel.png')}}?{{ time() }}" alt="Zenvoyc Dashboard"/>
            </div>
        </div>
    </x-section.hero>

    {{-- Feature 1: Invoicing & Global Compliance --}}
    <x-section.columns class="max-w-none md:max-w-6xl" id="features">
        <x-section.column>
            <div x-intersect="$el.classList.add('slide-in-top')">
                <x-heading.h6 class="text-primary-500 uppercase tracking-widest">
                    {{ __('Automation & Compliance') }}
                </x-heading.h6>
                <x-heading.h2 class="text-primary-900">
                    {{ __('Smart Invoices that speak the language of global trade.') }}
                </x-heading.h2>
            </div>

            <p class="mt-4 text-gray-600 leading-relaxed">
                {{ __('Zenvoyc automates your entire billing workflow. From professional creation to automated follow-ups, we ensure your business remains compliant with international standards without any manual effort.') }}
            </p>

            {{-- القائمة المحدثة بالمميزات التقنية --}}
            <div class="mt-8 space-y-5">
                {{-- الميزة الجديدة: التحويل البروتوكولي الذكي --}}
                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-primary-50 rounded-lg flex items-center justify-center">
                        <x-icon.fancy name="development" class="w-6 h-6" type="primary"/>
                    </div>
                    <div>
                        <span class="font-bold text-primary-900 block">{{ __('Cross-Border Protocol Bridge') }}</span>
                        <p class="text-sm text-gray-500">{{ __('Seamlessly transform Factur-X/ZUGFeRD (CII) to PEPPOL (UBL) for EU-wide digital invoicing compliance.') }}</p>
                    </div>
                </div>

                <div class="flex items-start gap-4">
                    <div class="flex-shrink-0 w-10 h-10 bg-primary-50 rounded-lg flex items-center justify-center">
                        <x-icon.fancy name="translatable" class="w-6 h-6" type="primary"/>
                    </div>
                    <div>
                        <span class="font-bold text-primary-900 block">{{ __('Multi-Currency & Global Taxes') }}</span>
                        <p class="text-sm text-gray-500">{{ __('Bill in any currency with automatic tax calculations based on local and international regulations.') }}</p>
                    </div>
                </div>
            </div>

            <p class="pt-8 font-semibold text-sm text-gray-400 uppercase tracking-wider">{{ __('Seamlessly Integrated with:') }}</p>
            <div class="flex items-center gap-4 pt-3">
                <img src="{{URL::asset('/images/payment-providers/stripe.png')}}" title="Stripe"
                     class="h-8 grayscale hover:grayscale-0 transition opacity-70 hover:opacity-100"/>
                <img src="{{URL::asset('/images/payment-providers/lemon-squeezy.png')}}" title="PayPal"
                     class="h-8 grayscale hover:grayscale-0 transition opacity-70 hover:opacity-100"/>
                <div class="h-6 w-[1px] bg-gray-200 mx-2"></div>
                <span
                    class="text-[10px] font-bold text-gray-400 border border-gray-200 px-2 py-1 rounded text-center">PEPPOL READY</span>
                <span
                    class="text-[10px] font-bold text-gray-400 border border-gray-200 px-2 py-1 rounded">ZUGFeRD</span>
            </div>
        </x-section.column>

        <x-section.column class="relative">
            {{-- صورة الفاتورة مع تأثير "الدرع" أو "التحقق" لزيادة الثقة --}}
            <div class="relative">
                <img src="{{URL::asset('/images/features/invoice-mockup.jpg')}}" alt="Zenvoyc Invoicing"
                     class="rounded-2xl shadow-2xl border border-gray-100">
                {{-- عنصر عائم يوضح عملية الـ Conversion برمجياً --}}
            </div>
        </x-section.column>
    </x-section.columns>

    {{-- Feature 2: Expense Tracking --}}
    <x-section.columns class="flex-wrap-reverse max-w-none md:max-w-6xl">
        <x-section.column>
            <img src="{{URL::asset('/images/features/expenidtures.png')}}" alt="Expense Tracking"
                 class="rounded-xl shadow-lg"/>
        </x-section.column>

        <x-section.column>
            <div x-intersect="$el.classList.add('slide-in-top')">
                <x-heading.h6 class="text-primary-500">
                    {{ __('Financial Clarity') }}
                </x-heading.h6>
                <x-heading.h2 class="text-primary-900">
                    {{ __('Track every penny effortlessly.') }}
                </x-heading.h2>
            </div>

            <p class="mt-4 text-gray-600 leading-relaxed">
                {{ __('Categorize your expenses, upload receipts via your phone, and monitor your cash flow in real-time. Knowing where your money goes is the first step to scaling your business.') }}
            </p>

            <x-button-link.primary href="#contact" class="mt-6">
                {{ __('Start Tracking Now') }}
            </x-button-link.primary>
        </x-section.column>
    </x-section.columns>
    {{-- Pricing & Plans Section --}}
    <section class="py-24 my-8 bg-white" id="pricing">
        <div class="max-w-6xl mx-auto px-6">
            {{-- Header --}}
            <div class="text-center mb-16" x-intersect="$el.classList.add('slide-in-top')">
                <x-pill class="text-primary-500 bg-primary-50 border border-primary-100 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider">
                    {{ __('Transparent Pricing') }}
                </x-pill>
                <x-heading.h2 class="my-6 text-primary-900 font-bold">
                    {{ __('Simple plans for every stage of growth') }}
                </x-heading.h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8 items-stretch">

                {{-- Plan 1: Starter (Monthly Access) --}}
                <div class="flex flex-col p-8 bg-white border-2 border-neutral-100 rounded-3xl shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-primary-900">{{ __('Essential') }}</h3>
                        <p class="text-gray-500 text-sm mt-2">{{ __('Perfect for freelancers and solo-entrepreneurs.') }}</p>
                    </div>
                    <div class="mb-8 font-extrabold text-primary-900 text-4xl">
                        $24<span class="text-sm font-normal text-gray-400">/mo</span>
                    </div>

                    <div class="space-y-4 mb-10 flex-1 border-t border-neutral-50 pt-6">
                        <div class="flex items-center gap-3 text-sm text-gray-600 font-medium">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            {{ __('Up to 20 Invoices / month') }}
                        </div>
                        <div class="flex items-center gap-3 text-sm text-gray-600 font-medium">
                            <svg class="w-5 h-5 text-green-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            {{ __('Expense Tracking') }}
                        </div>
                    </div>

                    <div class="mt-auto">
                        <x-button-link.primary-outline href="#contact" class="w-full justify-center !py-4 rounded-xl border-neutral-200 text-primary-900 hover:border-primary-500">
                            {{ __('Choose Essential') }}
                        </x-button-link.primary-outline>
                    </div>
                </div>

                {{-- Plan 2: Growth (Advanced Monthly Access) --}}
                <div class="flex flex-col p-8 bg-primary-900 border-2 border-primary-800 rounded-3xl shadow-2xl transform scale-105 z-10 relative overflow-hidden">
                    <div class="absolute top-0 right-0 bg-primary-500 text-white text-[10px] font-bold px-4 py-1 rounded-bl-xl uppercase tracking-widest">
                        {{ __('Most Popular') }}
                    </div>
                    <div class="mb-8">
                        <h3 class="text-xl font-bold text-white">{{ __('Professional') }}</h3>
                        <p class="text-primary-200 text-sm mt-2">{{ __('Advanced tools for growing agencies.') }}</p>
                    </div>
                    <div class="mb-8">
                        <span class="text-4xl font-extrabold text-white">$79</span>
                        <span class="text-primary-300">/{{ __('month') }}</span>
                    </div>
                    <ul class="space-y-4 mb-10 flex-1 text-primary-50">
                        <li class="flex items-center gap-3 text-sm">
                            <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            {{ __('Unlimited Invoices') }}
                        </li>
                        <li class="flex items-center gap-3 text-sm">
                            <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            {{ __('Multi-Company Support (Up to 5)') }}
                        </li>
                        <li class="flex items-center gap-3 text-sm">
                            <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            {{ __('Automated Tax Compliance') }}
                        </li>
                        <li class="flex items-center gap-3 text-sm">
                            <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            {{ __('Advanced Email Editor') }}
                        </li>
                    </ul>
                    <x-button-link.secondary href="#contact" class="w-full justify-center !py-4 shadow-xl">
                        {{ __('Go Pro Now') }}
                    </x-button-link.secondary>
                </div>
                {{-- Plan 3: Custom (Enterprise) --}}
                <div class="flex flex-col p-8 bg-neutral-50 border-2 border-neutral-100 rounded-3xl shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="mb-6">
                        <h3 class="text-xl font-bold text-primary-900">{{ __('Enterprise') }}</h3>
                        <p class="text-gray-500 text-sm mt-2">{{ __('Tailored workflows for your unique business needs.') }}</p>
                    </div>
                    <div class="mb-8 font-bold text-primary-900 text-2xl h-[40px] flex items-center">
                        {{ __('Customized') }}
                    </div>

                    <div class="space-y-4 mb-10 flex-1 border-t border-neutral-200 pt-6 text-gray-500 text-sm italic">
                        <p>{{ __('Need custom API integrations, specific compliance bridges, or multi-user roles for a large organization?') }}</p>
                        <p>{{ __('We build exactly what your business requires.') }}</p>
                    </div>

                    <div class="mt-auto">
                        <x-button-link.primary href="#contact" class="w-full justify-center !py-4 rounded-xl">
                            {{ __('Talk to an Expert') }}
                        </x-button-link.primary>
                    </div>
                </div>

            </div>
        </div>
    </section>
    {{-- Tech Stack Section --}}
    <div class="mx-4 mt-24 text-center" id="tech-stack">
        <x-heading.h6 class="text-primary-500 uppercase tracking-widest">
            {{ __('Enterprise Grade') }}
        </x-heading.h6>
        <x-heading.h2 class="text-primary-900">
            {{ __('Secure, Fast, and Reliable') }}
        </x-heading.h2>
    </div>

    <div class="p-4 mx-auto text-center max-w-4xl">
        <p class="text-gray-500">{{ __('Zenvoyc is built on the same technology used by the world\'s most secure financial platforms, ensuring your data remains private and your experience remains snappy.') }}</p>

        <div class="flex flex-wrap items-center justify-center gap-12 mt-10">
            <img src="{{URL::asset('/images/tech-stack/aws-svgrepo-com.svg')}}" title="Aws"
                 class="h-12 opacity-40 hover:opacity-100 transition"/>
            <img src="{{URL::asset('/images/tech-stack/cropped-logo.png')}}" title="Fnfe-MPE"
                 class="h-12 opacity-40 hover:opacity-100 transition"/>
            <img src="{{URL::asset('/images/tech-stack/ZUGFeRD_logo.svg')}}" title="ZUGFeRD"
                 class="h-12 opacity-40 hover:opacity-100 transition"/>
        </div>
    </div>

    {{-- Tab Slider: Specific Features --}}
    <div class="p-4 mt-24 text-center">
        <x-heading.h6 class="text-primary-500">{{ __('Comprehensive Tools') }}</x-heading.h6>
        <x-heading.h2 class="text-primary-900">{{ __('Everything your accountant would love.') }}</x-heading.h2>
    </div>

    <div class="mx-4">
        <x-tab-slider class="py-8 mt-6 border-2 md:max-w-6xl border-neutral-100 rounded-2xl">
            <x-slot name="tabNames">
                <x-tab-slider.tab-name controls="tab-1" active="true">{{ __('Auto-Generated') }}</x-tab-slider.tab-name>
                <x-tab-slider.tab-name controls="tab-2">{{ __('Email Theme Editor') }}</x-tab-slider.tab-name>
                <x-tab-slider.tab-name
                    controls="tab-3">{{ __('Multiple Businesses in One Place') }}</x-tab-slider.tab-name>
            </x-slot>

            <x-tab-slider.tab-content id="tab-1">
                <div class="mt-8 text-center px-6">
                    <x-heading.h4 class="text-primary-900 !font-semibold">
                        {{ __('Automated Billing & Client Portal') }}
                    </x-heading.h4>

                    <div class="max-w-3xl mx-auto mt-4 space-y-4">
                        <p class="text-gray-600 leading-relaxed">
                            {{ __('Put your revenue on autopilot. Zenvoyc allows you to schedule professional invoices to be generated and sent automatically at custom intervals—weekly, monthly, or annually.') }}
                        </p>

                    </div>
                    <img src="{{URL::asset('/images/features/invoices.png')}}"
                         class="w-full max-w-4xl mx-auto mt-12 drop-shadow-xl rounded-2xl h-auto object-cover"/>
                </div>
            </x-tab-slider.tab-content>
            <x-tab-slider.tab-content id="tab-2">
                <div class="mt-8 text-center px-6">
                    <x-heading.h4
                        class="text-primary-900 !font-semibold">{{ __('Fully Branded Email Experience') }}</x-heading.h4>
                    <p class="max-w-2xl mx-auto mt-4 text-gray-600">
                        {{ __('Your invoices, your style. Zenvoyc gives you complete control over how your invoice emails look. Choose from multiple professional layouts and customize colors, logos, and messaging to match your brand identity perfectly.') }}                    </p>
                    <img src="{{URL::asset('/images/features/email-editor.png')}}"
                         class="w-full max-w-4xl mx-auto mt-12 drop-shadow-xl rounded-2xl h-auto object-cover"
                         alt="Zenvoyc Email Editor"/>
                </div>
            </x-tab-slider.tab-content>
            <x-tab-slider.tab-content id="tab-3">
                <div class="mt-8 text-center px-6">
                    <x-heading.h4
                        class="text-primary-900 !font-semibold">{{ __('Manage Multiple Businesses in One Place') }}</x-heading.h4>
                    <p class="max-w-2xl mx-auto mt-4 text-gray-600">
                        {{ __('Why limit yourself to one? Zenvoyc lets you create and manage multiple companies under a single account. Switch between different business profiles instantly, with separate branding, tax settings, and invoice numbering for each.') }}       </p>
                    <img src="{{URL::asset('/images/features/comapny-settings.png')}}"
                         class="w-full max-w-4xl mx-auto mt-12 drop-shadow-xl rounded-2xl h-auto object-cover"/>
                </div>
            </x-tab-slider.tab-content>
            {{-- ... يمكنك إضافة محتوى بقية التابات هنا بنفس النمط ... --}}
        </x-tab-slider>
    </div>

    {{-- Final CTA --}}
    <div class="bg-primary-900 my-12 py-16 px-4 rounded-3xl mx-4 md:mx-auto max-w-6xl text-center">
        <x-heading.h2 class="text-white">{{ __('Ready to transform your business accounting?') }}</x-heading.h2>
        <p class="text-primary-100 mt-4 max-w-2xl mx-auto">
            {{ __('Join hundreds of freelancers and agency owners who trust Zenvoyc to power their financial growth.') }}
        </p>

    </div>

    {{-- FAQ Section --}}
    {{-- Contact/Lead Generation Section --}}
    <section class="py-12 bg-white" id="contact">
        <div class="max-w-4xl mx-auto px-6 text-center">
            <div x-intersect="$el.classList.add('slide-in-top')">
                <x-pill
                    class="text-primary-500 bg-primary-50 border border-primary-100 px-4 py-1.5 rounded-full text-xs font-bold uppercase tracking-wider">
                    {{ __('Direct Support') }}
                </x-pill>
                <x-heading.h2 class="mt-6 text-primary-900 font-bold">
                    {{ __('Ready to scale your financial workflow?') }}
                </x-heading.h2>
                <p class="mt-4 text-gray-600 max-w-2xl mx-auto">
                    {{ __('Leave your details below, and our team will get back to you with a personalized Zenvoyc walkthrough.') }}
                </p>
            </div>

            {{-- الفورم بتصميم يتماشى مع Tab Slider --}}
            <div x-data="{ submitted: false }"
                 class="mt-12 max-w-lg mx-auto p-1 md:p-8 bg-white rounded-3xl border-2 border-neutral-100 shadow-xl relative overflow-hidden">

                {{-- حالة النجاح --}}
                <div x-show="submitted" x-transition.duration.500ms class="py-12 px-6">
                    <div
                        class="w-20 h-20 bg-green-50 text-green-500 rounded-full flex items-center justify-center mx-auto mb-6 border border-green-100">
                        <x-icon.fancy name="heroicon-o-check-circle" class="w-10 h-10"/>
                    </div>
                    <h3 class="text-primary-900 font-bold text-2xl">{{ __('We\'ve received your request!') }}</h3>
                    <p class="text-gray-500 mt-3 leading-relaxed">{{ __('One of our experts will reach out to your work email within 24 hours.') }}</p>
                    <button @click="submitted = false"
                            class="mt-8 text-primary-500 font-bold hover:underline transition">
                        {{ __('Back to form') }}
                    </button>
                </div>

                {{-- نموذج الإدخال --}}
                <form x-show="!submitted" @submit.prevent="submitted = true" class="space-y-6 text-left p-6 md:p-0">
                    <div>
                        <label class="block text-sm font-semibold text-primary-900 mb-2">{{ __('Full Name') }}</label>
                        <input type="text" required placeholder="Your Name"
                               class="w-full bg-neutral-50 border border-neutral-200 rounded-xl px-4 py-3.5 text-primary-900 placeholder-gray-400 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/10 transition-all">
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-primary-900 mb-2">{{ __('Work Email') }}</label>
                        <input type="email" required placeholder="youremail@email.com"
                               class="w-full bg-neutral-50 border border-neutral-200 rounded-xl px-4 py-3.5 text-primary-900 placeholder-gray-400 focus:outline-none focus:border-primary-500 focus:ring-2 focus:ring-primary-500/10 transition-all">
                    </div>

                    <div>
                        <label
                            class="block text-sm font-semibold text-primary-900 mb-2">{{ __('Business Type') }}</label>
                        <div class="relative">
                            <select
                                class="w-full bg-neutral-50 border border-neutral-200 rounded-xl px-4 py-3.5 text-primary-900 focus:outline-none focus:border-primary-500 transition-all appearance-none cursor-pointer">
                                <option>{{ __('Freelancer / Agency') }}</option>
                                <option>{{ __('SaaS / Tech Company') }}</option>
                                <option>{{ __('E-commerce Business') }}</option>
                                <option>{{ __('Other') }}</option>
                            </select>

                        </div>
                    </div>

                    <div class="pt-4">
                        <button type="submit"
                                class="w-full py-4 bg-primary-900 hover:bg-primary-800 text-white font-bold rounded-xl shadow-lg hover:shadow-primary-900/20 transition-all transform active:scale-95 flex items-center justify-center gap-2">
                            <span>{{ __('Get Expert Consultation') }}</span>
                        </button>
                    </div>

                    <p class="text-[11px] text-gray-400 text-center leading-tight">
                        {{ __('By requesting a walkthrough, you agree to our terms of service and professional privacy standards.') }}
                    </p>
                </form>
            </div>
        </div>
    </section>
</x-layouts.app>
