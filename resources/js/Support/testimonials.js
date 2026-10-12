/**
 * The QuoteBlock props of a testimonial as the server passes it (Testimonial::toDisplay()).
 */
export function quoteProps(testimonial) {
    return {
        quote: testimonial.quote,
        name: testimonial.author_name,
        context: testimonial.author_context,
        date: testimonial.travelled_in,
        photo: testimonial.photo,
        paragraphs: testimonial.paragraphs,
        linkUrl: testimonial.link_url,
        linkLabel: testimonial.link_label,
    };
}
