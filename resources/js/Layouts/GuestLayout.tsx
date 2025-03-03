import ApplicationLogo from '@/Components/ApplicationLogo';
import {Link} from '@inertiajs/react';
import {PropsWithChildren} from 'react';
import Container from "react-bootstrap/Container";
import Row from "react-bootstrap/Row";
import Col from "react-bootstrap/Col";

export default function Guest({ children }: PropsWithChildren) {
    return (
        <div className='position-absolute top-50 start-50 translate-middle'>
            <Container fluid>
                {/*<Row className="align-items-center">*/}
                <Row className='m-4'>
                    {/*<Col>*/}
                    <Col className="text-center w-50 mx-auto ">
                <Link href="/">
                    <ApplicationLogo height={'5rem'} width={'5rem'} className="fill-current text-gray-500"/>
                </Link>
                    </Col>
                </Row>
                <Row>
                {children}
                </Row>
            </Container>
        </div>
    );
}
