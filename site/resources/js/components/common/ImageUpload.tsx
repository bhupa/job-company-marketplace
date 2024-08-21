import React, { useState } from 'react';

import { AddCircle, Edit } from '@assets/icons';

interface Props {
  hidden?: boolean;
  uploadRef?: any;
  image?: string | null;
  onImageSelect: (image: string, file: File) => void;
}

const ImageUpload = ({
  onImageSelect,
  image: initialImage,
  uploadRef,
  hidden,
}: Props) => {
  const [image, setImage] = useState<string | null>(initialImage || null);

  const handleImageChange = (e: React.ChangeEvent<HTMLInputElement>) => {
    const file = e.target.files?.[0] ?? null;
    const reader = new FileReader();

    reader.onload = (event: any) => {
      const base64Image = event.target.result;
      setImage(base64Image);
      file && onImageSelect(base64Image, file);
    };

    if (file) {
      reader.readAsDataURL(file);
    }
  };

  return (
    <div
      className={`${hidden ? 'hidden' : 'flex'} relative justify-center items-center w-[200px] h-[200px] border-dotted rounded-md border border-GRAY_A6A6A6`}>
      <input
        ref={uploadRef}
        type="file"
        accept="image/*"
        className="absolute w-[200px] h-[200px] opacity-0 cursor-pointer"
        onChange={handleImageChange}
      />
      <div className="flex justify-center items-center w-full h-full">
        {image ? (
          <img
            src={image}
            alt="Uploaded"
            className="w-[200px] h-[200px] object-contain"
          />
        ) : (
          <>
            <AddCircle className="text-BLUE_004D80" />
          </>
        )}
      </div>
      <div
        onClick={() => {
          uploadRef.current?.click();
        }}
        className="absolute -bottom-1 -right-2 bg-RED_FF4D4D rounded-full h-7 w-7 flex justify-center items-center cursor-pointer">
        <Edit className={'text-white'} width={14} />
      </div>
    </div>
  );
};

export default ImageUpload;
