enum TextType {
  heading1 = 'heading1',
  heading2 = 'heading2',
  body1 = 'body1',
  body2 = 'body2',
  caption1 = 'caption1',
  caption2 = 'caption2',
}

export const typography: { [key in keyof typeof TextType]: string } = {
  heading1: 'text-2xl font-bold',
  heading2: 'text-xl',
  body1: 'text-lg',
  body2: 'text-base',
  caption1: 'text-sm',
  caption2: 'text-xs',
};

interface TextProps {
  type: keyof typeof typography;
  children: string | string[] | any;
  className?: string;
  bold?: boolean;
  tagName?: keyof JSX.IntrinsicElements;
  textTypeClassName?: string; // override class of type value
}

const Title: React.FC<TextProps> = ({
  children,
  type = 'body1',
  textTypeClassName,
  className = 'text-black',
  bold,
  tagName = 'span',
  ...rest
}) => {
  const TagName = tagName;
  return (
    <TagName
      className={`${textTypeClassName ? textTypeClassName : typography[type]} ${bold ? 'font-bold' : ''} ${className}`}
      {...rest}>
      {children}
    </TagName>
  );
};

export default Title;
