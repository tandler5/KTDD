export const Link = {
    props: ['href', 'class'],
    template: '<a :href="href" :class="class"><slot /></a>',
};

export const Head = {
    props: ['title'],
    template: '<title>{{ title }}</title>',
};

export const useForm = (data) => ({
    ...data,
    processing: false,
    errors: {},
    post: vi.fn(),
    reset: vi.fn(),
});

export const route = (name) => `/${name}`;