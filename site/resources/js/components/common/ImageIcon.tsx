import React, { ImgHTMLAttributes } from 'react';

const ImageIcon = (props: ImgHTMLAttributes<HTMLImageElement>) => {
  return <img width={16} height={16} className="object-contain" {...props} />;
};

export default ImageIcon;
